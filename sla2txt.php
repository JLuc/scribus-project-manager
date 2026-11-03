#!/usr/bin/env php
<?php
/**
 * sla2txt.php — Extrait le texte des frames de documents Scribus (.sla)
 *
 * Usage :
 *   php sla2txt.php [-o sortie.txt] fichier1.sla [fichier2.sla ...]
 *
 * - Les noms de fichiers peuvent être donnés avec ou sans l'extension .sla,
 *   à plat ou rangés dans un dossier de même nom (truc -> truc/truc.sla) ;
 *   un argument peut contenir plusieurs noms séparés par des espaces
 *   (« truc paf » -> truc/truc.sla et paf/paf.sla) ; les arguments vides sont ignorés.
 * - Extrait uniquement les textes des frames des pages (pas ceux des pages maîtres).
 * - Aucune mise en forme : un double saut de ligne à la fin de chaque paragraphe.
 * - Entre les textes de deux fichiers différents, un séparateur est inséré
 *   (voir les constantes SEPARATEUR_AVANT / SEPARATEUR_APRES ci-dessous).
 */

declare(strict_types=1);

// Séparateur inséré entre les textes de 2 fichiers : AVANT + nom du fichier + APRES
const SEPARATEUR_AVANT = "\n\n------------------------\n";
const SEPARATEUR_APRES = "\n\n";

// Fichier de sortie par défaut (dans le dossier courant)
const SORTIE_PAR_DEFAUT = 'textes.txt';

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Ce script doit être lancé depuis le terminal.\n");
    exit(1);
}

function usage(): void
{
    $script = basename($_SERVER['argv'][0] ?? 'sla2txt.php');
    echo <<<TXT
Usage : php {$script} [-o sortie.txt] fichier1.sla [fichier2.sla ...]

Extrait le texte des frames de documents Scribus (.sla), hors pages maîtres,
sans mise en forme, et l'écrit dans un fichier texte UTF-8.

Options :
  -o, --output FICHIER   fichier de sortie (défaut : textes.txt ; "-" = sortie standard)
  -h, --help             affiche cette aide

TXT;
}

/**
 * Lit et parse un fichier .sla (XML, éventuellement compressé en gzip).
 */
function chargerSla(string $chemin): DOMDocument
{
    if (!is_file($chemin) || !is_readable($chemin)) {
        throw new RuntimeException('fichier introuvable ou illisible');
    }
    $contenu = file_get_contents($chemin);
    if ($contenu === false) {
        throw new RuntimeException('lecture impossible');
    }

    // Scribus peut enregistrer en .sla.gz
    if (strncmp($contenu, "\x1f\x8b", 2) === 0) {
        if (!function_exists('gzdecode')) {
            throw new RuntimeException("fichier compressé mais l'extension PHP zlib est absente");
        }
        $decode = gzdecode($contenu);
        if ($decode === false) {
            throw new RuntimeException('décompression gzip impossible');
        }
        $contenu = $decode;
    }

    $dom = new DOMDocument();
    $anciennes = libxml_use_internal_errors(true);
    libxml_clear_errors();
    $ok = $dom->loadXML($contenu, LIBXML_NONET | LIBXML_PARSEHUGE);
    $erreurs = libxml_get_errors();
    libxml_clear_errors();
    libxml_use_internal_errors($anciennes);

    if (!$ok) {
        $msg = $erreurs ? trim($erreurs[0]->message) . ' (ligne ' . $erreurs[0]->line . ')' : 'XML invalide';
        throw new RuntimeException('XML invalide : ' . $msg);
    }
    return $dom;
}

/**
 * Vrai si l'élément n'est pas un vrai frame de page, c'est-à-dire s'il appartient à :
 * - une page maître : éléments <MASTEROBJECT> (format récent)
 *   ou <PAGEOBJECT OnMasterPage="NomDeLaMaster"> (format ancien) ;
 * - un objet « en réserve » du document, qui n'est posé sur aucune page :
 *   <FRAMEOBJECT> (cadres en ligne) et <Pattern>/<PatternItem> (motifs et symboles).
 */
function estSurPageMaitre(DOMElement $el): bool
{
    for ($n = $el; $n instanceof DOMElement; $n = $n->parentNode) {
        if (in_array($n->nodeName, ['MASTEROBJECT', 'FRAMEOBJECT', 'Pattern', 'PatternItem'], true)) {
            return true;
        }
        if ($n->hasAttribute('OnMasterPage') && trim($n->getAttribute('OnMasterPage')) !== '') {
            return true;
        }
    }
    return false;
}

/**
 * Nettoie le contenu d'un attribut CH d'un <ITEXT>.
 */
function nettoyerTexte(string $ch): string
{
    return strtr($ch, [
        "\u{00A0}" => ' ',   // espace insécable -> espace
        "\u{202F}" => ' ',   // espace fine insécable -> espace
        "\u{00AD}" => '',    // trait d'union conditionnel
        "\u{2028}" => "\n",  // saut de ligne forcé
        "\x1C"     => "\n",  // saut de ligne forcé (anciens formats)
        "\x18"     => '-',   // trait d'union insécable (anciens formats)
        "\x19"     => '',    // saut de colonne (anciens formats)
        "\x1A"     => '',    // saut de frame (anciens formats)
    ]);
}

/**
 * Extrait les paragraphes d'un conteneur de texte (<StoryText>, ou <PAGEOBJECT>
 * dans les anciens formats où <ITEXT> est directement sous l'objet).
 *
 * @return string[] liste de paragraphes non vides
 */
function extraireParagraphes(DOMElement $conteneur): array
{
    $paragraphes = [];
    $courant = '';

    $terminer = function () use (&$paragraphes, &$courant): void {
        // On retire les espaces, tabulations et sauts de ligne parasites en début/fin
        // de paragraphe ; un paragraphe vide est ignoré.
        $texte = trim($courant);
        if ($texte !== '') {
            $paragraphes[] = $texte;
        }
        $courant = '';
    };

    foreach ($conteneur->childNodes as $n) {
        if (!$n instanceof DOMElement) {
            continue;
        }
        switch ($n->nodeName) {
            case 'ITEXT':
                $ch = $n->getAttribute('CH');
                // Séparateurs de paragraphe éventuellement présents dans le texte
                $morceaux = preg_split('/[\r\x{2029}]/u', $ch);
                if ($morceaux === false) {
                    $morceaux = [$ch];
                }
                foreach ($morceaux as $i => $morceau) {
                    if ($i > 0) {
                        $terminer();
                    }
                    $courant .= nettoyerTexte($morceau);
                }
                break;
            case 'para':        // fin de paragraphe
                $terminer();
                break;
            case 'breakline':   // saut de ligne forcé à l'intérieur d'un paragraphe
                $courant .= "\n";
                break;
            case 'tab':
                $courant .= "\t";
                break;
            case 'nbspace':
                $courant .= ' ';
                break;
            case 'nbhyphen':
                $courant .= '-';
                break;
            default:
                // trail, DefaultStyle, MARK, var, OBJECT, softhyphen, zwspace... : ignorés
                break;
        }
    }
    $terminer(); // dernier paragraphe (pas de <para/> final dans Scribus)

    return $paragraphes;
}

/**
 * Retourne tous les paragraphes des frames des pages (hors pages maîtres)
 * d'un document, dans l'ordre du fichier.
 *
 * @return string[]
 */
function extraireDocument(string $chemin): array
{
    $dom = chargerSla($chemin);
    $xpath = new DOMXPath($dom);

    // <StoryText> (formats récents) ou <PAGEOBJECT> contenant directement des <ITEXT> (anciens formats)
    $conteneurs = $xpath->query('//StoryText | //PAGEOBJECT[not(StoryText) and (ITEXT or para)]');
    if ($conteneurs === false) {
        return [];
    }

    $tous = [];
    foreach ($conteneurs as $conteneur) {
        if (!$conteneur instanceof DOMElement || estSurPageMaitre($conteneur)) {
            continue;
        }
        foreach (extraireParagraphes($conteneur) as $p) {
            $tous[] = $p;
        }
    }
    return $tous;
}

/**
 * Cherche le fichier .sla désigné par un nom, avec ou sans extension, posé
 * directement ou rangé dans un dossier de même nom. Pour « truc », essaie dans l'ordre :
 *   truc.sla, truc.SLA, truc.sla.gz, truc/truc.sla, truc/truc.SLA, truc/truc.sla.gz, « truc » lui-même.
 * Un nom qui a déjà l'extension (truc.sla, truc.sla.gz) est cherché tel quel, puis dans truc/.
 *
 * @return string|null le chemin trouvé, ou null
 */
function chercherFichier(string $nom): ?string
{
    $aExtension = preg_match('/\.sla(\.gz)?$/i', $nom) === 1;
    $dossier = dirname($nom);
    $prefixe = ($dossier === '.' && strncmp($nom, './', 2) !== 0) ? '' : $dossier . '/';
    $base = basename($nom);

    $candidats = [];
    if ($aExtension) {
        $candidats[] = $nom;
        $sansExt = preg_replace('/\.sla(\.gz)?$/i', '', $base);
        $candidats[] = $prefixe . $sansExt . '/' . $base;
    } else {
        foreach (['.sla', '.SLA', '.sla.gz'] as $ext) {
            $candidats[] = $nom . $ext;
        }
        foreach (['.sla', '.SLA', '.sla.gz'] as $ext) {
            $candidats[] = $nom . '/' . $base . $ext;
        }
        $candidats[] = $nom;
    }

    foreach ($candidats as $c) {
        if (is_file($c)) {
            return $c;
        }
    }
    return null;
}

/**
 * Transforme un argument en liste de fichiers à traiter.
 * 1. L'argument entier désigne un fichier (nom avec espaces compris) : on le prend.
 * 2. Sinon, s'il contient des espaces, on le considère comme une liste de noms
 *    (« truc paf » -> truc/truc.sla et paf/paf.sla) ; si aucun mot ne correspond à
 *    un fichier, on garde l'argument entier pour que l'erreur reste lisible.
 * 3. Sinon l'argument est conservé tel quel (et sera signalé introuvable).
 *
 * @return string[]
 */
function resoudreArgument(string $arg): array
{
    $entier = chercherFichier($arg);
    if ($entier !== null) {
        return [$entier];
    }

    $mots = preg_split('/\s+/', trim($arg), -1, PREG_SPLIT_NO_EMPTY);
    if ($mots !== false && count($mots) > 1) {
        $resolus = [];
        $trouve = false;
        foreach ($mots as $mot) {
            $f = chercherFichier($mot);
            $trouve = $trouve || $f !== null;
            $resolus[] = $f ?? $mot;
        }
        if ($trouve) {
            return $resolus;
        }
    }
    return [$arg];
}

// ---------------------------------------------------------------------------
// Programme principal
// ---------------------------------------------------------------------------

$args = array_slice($argv, 1);
$sortie = SORTIE_PAR_DEFAUT;
$fichiers = [];

for ($i = 0, $n = count($args); $i < $n; $i++) {
    $a = $args[$i];
    if ($a === '-h' || $a === '--help') {
        usage();
        exit(0);
    } elseif ($a === '-o' || $a === '--output') {
        if (!isset($args[$i + 1])) {
            fwrite(STDERR, "Erreur : l'option $a attend un nom de fichier.\n");
            exit(2);
        }
        $sortie = $args[++$i];
    } elseif (strncmp($a, '--output=', 9) === 0) {
        $sortie = substr($a, 9);
    } elseif ($a === '--') {
        $fichiers = array_merge($fichiers, array_slice($args, $i + 1));
        break;
    } elseif (strlen($a) > 1 && $a[0] === '-') {
        fwrite(STDERR, "Erreur : option inconnue « $a ».\n");
        usage();
        exit(2);
    } else {
        $fichiers[] = $a;
    }
}

// Arguments vides ou blancs ignorés ; retour chariot final retiré (listes venant de fichiers CRLF)
$fichiers = array_values(array_filter(
    array_map(function (string $f): string { return rtrim($f, "\r"); }, $fichiers),
    function (string $f): bool { return trim($f) !== ''; }
));

if (!$fichiers) {
    usage();
    exit(2);
}

$resultat = '';
$premier = true;
$echecs = 0;

// Résolution des noms : extension facultative, dossier de même nom, plusieurs noms par argument
$liste = [];
foreach ($fichiers as $argument) {
    foreach (resoudreArgument($argument) as $f) {
        $liste[] = $f;
    }
}

foreach ($liste as $fichier) {
    try {
        $texte = implode("\n\n", extraireDocument($fichier));
    } catch (Throwable $e) {
        fwrite(STDERR, "Erreur sur « $fichier » : " . $e->getMessage() . "\n");
        $echecs++;
        continue;
    }

    if (!$premier) {
        $resultat .= SEPARATEUR_AVANT . basename($fichier) . SEPARATEUR_APRES;
    }
    $resultat .= $texte;
    $premier = false;
}

if ($premier) {
    fwrite(STDERR, "Aucun fichier n'a pu être traité.\n");
    exit(1);
}

$resultat .= "\n";

if ($sortie === '-') {
    fwrite(STDOUT, $resultat);
} else {
    if (file_put_contents($sortie, $resultat) === false) {
        fwrite(STDERR, "Erreur : impossible d'écrire « $sortie ».\n");
        exit(1);
    }
    $ok = count($liste) - $echecs;
    fwrite(STDERR, "$ok fichier(s) traité(s) -> $sortie\n");
}

exit($echecs > 0 ? 1 : 0);
