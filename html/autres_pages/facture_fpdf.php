<?php
require_once 'model/Offre.php';
require_once 'auth.php';
require_once 'model/Compte.php';
require_once 'fpdf186/fpdf.php';

define('EURO', chr(128));

// Récupérer ls information du compte

$compte = Compte::from_db(Auth\id_pro_connecte());
if ($compte === false) fail_404();


function convertToIso88591($string)
{
    return mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
}

// Créer une classe pour la facture (facultatif)
class FacturePDF extends FPDF
{
    // En-tête

    // Pied de page
    function Footer()
    {
        $this->SetY(-15);                                                    // À 15 mm du bas
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');  // Numéro de page
    }

    function Row($data)
    {
        // Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        // Issue a page break first if needed
        $this->CheckPageBreak($h);
        // Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            // Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            // Draw the border
            $this->Rect($x, $y, $w, $h);
            // Print the text
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            // Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        // Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        // If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        // Compute the number of lines a MultiCell of width w will take
        if (!isset($this->CurrentFont))
            $this->Error('No font has been set');
        $cw = $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s    = str_replace("\r", '', (string) $txt);
        $nb   = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i   = 0;
        $j   = 0;
        $l   = 0;
        $nl  = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j   = $i;
                $l   = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j   = $i;
                $l   = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    function Table($header, $data)
    {
        // Largeurs des colonnes
        $this->widths = [50, 37, 40, 30, 30];       // Largeurs des colonnes
        $this->aligns = ['L', 'L', 'L', 'C', 'R'];  // Alignement des colonnes

        // Afficher les en-têtes
        $this->SetFont('Arial', 'B', 12);  // Police pour les en-têtes
        $yStart = $this->GetY();           // Position Y initiale pour l'en-tête

        foreach ($header as $i => $col) {
            // Affiche l'en-tête en deux lignes si nécessaire
            if ($i == 1) {                                                               // Gestion particulière pour "Type d'abonnement"
                $x = $this->GetX();
                $y = $this->GetY();
                $this->MultiCell($this->widths[$i], 7, convertToIso88591($col), 1, 'C');
                $this->SetXY($x + $this->widths[$i], $y);
            } else {
                $this->Cell($this->widths[$i], 14, convertToIso88591($col), 1, 0, 'C');  // En-tête simple
            }
        }
        $this->Ln();

        // Afficher les données
        $this->SetFont('Arial', '', 12);  // Police normale pour les données
        foreach ($data as $row) {
            $this->Row(array_map('convertToIso88591', $row));
        }
    }
}

// Créer une instance de la classe
$pdf = new FacturePDF();
$pdf->AliasNbPages();  // Pour afficher le nombre total de pages
$pdf->AddPage();       // Ajouter une page

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Facture', 0, 1, 'C');  // Titre centré
$pdf->Ln(10);

// Logo
$pdf->Image('../images/logo_vertical_big.jpg', 10, 10);  // Chemin du logo, x, y, largeur
$pdf->Ln();                                              // Saut de ligne

// information PacteS

$pdf->Ln();
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Entreprise : TripenArvor', 0, 1, 'R');
$pdf->Cell(0, 10, 'Adresse : 1 rue Edouard Branly', 0, 1, 'R');
$pdf->Cell(0, 10, 'Email : TripenArvor@gamail.com', 0, 1, 'R');
$pdf->Cell(0, 10, 'Tel. : 02 96 00 00 00', 0, 1, 'R');
$pdf->Cell(0, 10, 'Site : https://413.ventsdouest.dev', 0, 1, 'R');
// Informations sur le client
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 10, convertToIso88591("Client : $compte->denomination"), 0, 1);
$pdf->Cell(100, 10, convertToIso88591('Adresse : ' . $compte->adresse), 0, 1);
$pdf->Cell(100, 10, convertToIso88591('Email : ' . $compte->email), 0, 1);
$pdf->Cell(100, 10, "Tel. : $compte->telephone", 0, 1);

$pdf->Cell(100, 10, 'Date : ' . date('d/m/Y'), 0, 1);
$pdf->Ln(10);  // Saut de ligne

// Tableau des offre
$header           = ['Titre', "Type d'abonnement", 'Catégorie', 'Jours en ligne', 'Prix HT'];
$data             = [];
$resultat_global  = 0;
$id_professionnel = Auth\exiger_connecte_pro();
$offres           = Offre::from_db_all_ordered($id_professionnel);
foreach ($offres as $offre) {
    $resultat_offre   = round($offre->en_ligne_ce_mois_pendant->days * $offre->abonnement->prix_journalier, 2);
    $resultat_global += $resultat_offre;

    $data[] = [$offre->titre, $offre->abonnement->libelle, $offre->categorie, $offre->en_ligne_ce_mois_pendant->days, $resultat_offre];
}

$pdf->Table($header, $data);
$pdf->Ln(10);  // Saut de ligne

// Total
$pdf->SetFont('Arial', 'B', 12);
// total ht
$pdf->Cell(144, 10, 'Total HT', 1, 0, 'R');
$pdf->Cell(48, 10, $resultat_global . ' ' . EURO, 1, 1, 'C');
// TVA
$tva = round($resultat_global * 0.2, 2);
$pdf->Cell(144, 10, 'TVA (20%)', 1, 0, 'R');
$pdf->Cell(48, 10, $tva . ' ' . EURO, 1, 1, 'C');
// Total TTC
$resultat_global_ttc = round($resultat_global + $resultat_global * 0.2, 2);  // ne fonctionne pas avec un '+='
$pdf->Cell(144, 10, 'Total TTC', 1, 0, 'R');
$pdf->Cell(48, 10, $resultat_global_ttc . ' ' . EURO, 1, 1, 'C');

// Générer et afficher le PDF
$pdf->Output('I', "facture_Pact_$compte->denomination.pdf");  // I = afficher dans le navigateur, D = télécharger
?>





