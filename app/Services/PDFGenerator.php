<?php
namespace App\Services;

use Mpdf\Mpdf;
use App\Models\Entities\NoteFrais;
use App\Models\Entities\Deplacement;
use App\Models\Entities\User;

class PDFGenerator
{
    private Mpdf $mpdf;

    public function __construct()
    {
        $this->mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10,
        ]);
    }

    /**
     * Génère un PDF de note de frais avec tous les détails
     */
    public function generateNoteFraisPDF(
        NoteFrais $note,
        Deplacement $deplacement,
        User $employe,
        array $details,
        array $categories
    ): string {
        // Construire le HTML
        $html = $this->buildNoteFraisHTML($note, $deplacement, $employe, $details, $categories);
        
        // Écrire dans le PDF
        $this->mpdf->WriteHTML($html);
        
        // Retourner le contenu du PDF
        return $this->mpdf->Output('', 'S'); // 'S' = return as string
    }

    /**
     * Télécharge directement le PDF
     */
    public function downloadNoteFraisPDF(
        NoteFrais $note,
        Deplacement $deplacement,
        User $employe,
        array $details,
        array $categories,
        string $filename = null
    ): void {
        $html = $this->buildNoteFraisHTML($note, $deplacement, $employe, $details, $categories);
        $this->mpdf->WriteHTML($html);
        
        if (!$filename) {
            $filename = 'Note_Frais_' . $note->getId() . '_' . date('Ymd') . '.pdf';
        }
        
        $this->mpdf->Output($filename, 'D'); // 'D' = force download
    }

    /**
     * Sauvegarde le PDF sur le serveur
     */
    public function saveNoteFraisPDF(
        NoteFrais $note,
        Deplacement $deplacement,
        User $employe,
        array $details,
        array $categories,
        string $filepath
    ): bool {
        $html = $this->buildNoteFraisHTML($note, $deplacement, $employe, $details, $categories);
        $this->mpdf->WriteHTML($html);
        
        $this->mpdf->Output($filepath, 'F'); // 'F' = save to file
        return file_exists($filepath);
    }

    /**
     * Construction du HTML pour la note de frais
     */
    private function buildNoteFraisHTML(
        NoteFrais $note,
        Deplacement $deplacement,
        User $employe,
        array $details,
        array $categories
    ): string {
        // Calculer les totaux
        $categoriesMap = [];
        foreach ($categories as $cat) {
            $categoriesMap[$cat->getId()] = $cat;
        }

        $grandTotalVeloce = 0;
        $grandTotalPerso = 0;
        $totalNotes = count($details);

        // Regrouper par catégorie
        $detailsByCategory = [];
        foreach ($details as $d) {
            $catId = $d->getCategorieId();
            if (!isset($detailsByCategory[$catId])) {
                $detailsByCategory[$catId] = [];
            }
            $detailsByCategory[$catId][] = $d;
            $grandTotalVeloce += $d->getMontantVeloce();
            $grandTotalPerso += $d->getMontantPersonnel();
        }

        // Map des statuts
        $statutLabels = [
            'brouillon' => 'Brouillon',
            'soumis' => 'Soumise',
            'valide_manager' => 'Validée Manager',
            'rejetee_manager' => 'Rejetée Manager',
            'approuve' => 'Approuvée',
            'rejetee_admin' => 'Rejetée Admin'
        ];

        $statutColors = [
            'brouillon' => '#ffc107',
            'soumis' => '#0dcaf0',
            'valide_manager' => '#198754',
            'rejetee_manager' => '#dc3545',
            'approuve' => '#198754',
            'rejetee_admin' => '#dc3545'
        ];

        $statutLabel = $statutLabels[$note->getStatut()] ?? 'Inconnu';
        $statutColor = $statutColors[$note->getStatut()] ?? '#6c757d';

        // Construction du HTML
        ob_start();
        ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 20mm;
            @top-right {
                content: "Page " counter(page) " sur " counter(pages);
                font-size: 9pt;
                color: #666;
            }
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }
        
        /* Header */
        .header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .logo {
            font-size: 24pt;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .document-title {
            font-size: 18pt;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        
        /* Info Box */
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }
        
        .info-row {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #667eea;
            display: inline-block;
            width: 150px;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            font-size: 11pt;
        }
        
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th {
            background: #667eea;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        
        td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .category-header {
            background: #e9ecef;
            font-weight: bold;
            color: #495057;
        }
        
        .detail-row {
            background: white;
        }
        
        /* Totaux */
        .totals-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .total-row:last-child {
            border-bottom: none;
            font-size: 14pt;
            font-weight: bold;
            padding-top: 15px;
        }
        
        .total-label {
            font-weight: 600;
        }
        
        .total-amount {
            font-weight: bold;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            font-size: 8pt;
            color: #6c757d;
            text-align: center;
        }
        
        /* Signatures */
        .signatures {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px;
        }
        
        .signature-line {
            border-top: 2px solid #333;
            margin-top: 60px;
            padding-top: 5px;
            font-weight: bold;
        }
        
        /* Colors */
        .text-success { color: #198754; }
        .text-warning { color: #ffc107; }
        .text-danger { color: #dc3545; }
        .text-muted { color: #6c757d; font-size: 9pt; }
        
        /* Utilities */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .mt-4 { margin-top: 20px; }
        .mb-4 { margin-bottom: 20px; }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">VÉLOCE</div>
    <div class="document-title">NOTE DE FRAIS</div>
    <div class="text-muted">Référence: NF-<?= str_pad($note->getId(), 6, '0', STR_PAD_LEFT) ?></div>
    <div class="text-muted">Date d'édition: <?= date('d/m/Y à H:i') ?></div>
</div>

<!-- STATUT -->
<div class="text-center mb-4">
    <span class="status-badge" style="background-color: <?= $statutColor ?>;">
        <?= strtoupper($statutLabel) ?>
    </span>
</div>

<!-- INFORMATIONS DÉPLACEMENT -->
<div class="info-box">
    <h3 style="margin-top: 0; color: #667eea;">Informations du Déplacement</h3>
    <div class="info-row">
        <span class="info-label">Titre:</span>
        <span><?= htmlspecialchars($deplacement->getTitre()) ?></span>
    </div>
    <div class="info-row">
        <span class="info-label">Lieu:</span>
        <span><?= htmlspecialchars($deplacement->getLieu()) ?></span>
    </div>
    <div class="info-row">
        <span class="info-label">Période:</span>
        <span>Du <?= date('d/m/Y', strtotime($deplacement->getDateDepart())) ?> 
              au <?= date('d/m/Y', strtotime($deplacement->getDateRetour())) ?></span>
    </div>
    <div class="info-row">
        <span class="info-label">Employé:</span>
        <span><?= htmlspecialchars($employe->getNom()) ?> (<?= htmlspecialchars($employe->getEmail()) ?>)</span>
    </div>
</div>

<!-- DÉTAILS DES FRAIS -->
<h3 style="color: #667eea; border-bottom: 2px solid #667eea; padding-bottom: 5px;">Détail des Frais</h3>

<table>
    <thead>
        <tr>
            <th style="width: 30%;">Catégorie / Description</th>
            <th style="width: 15%;">Date</th>
            <th style="width: 15%; text-align: right;">VÉLOCE</th>
            <th style="width: 15%; text-align: right;">Personnel</th>
            <th style="width: 15%; text-align: right;">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $cat): ?>
            <?php if (!isset($detailsByCategory[$cat->getId()])) continue; ?>
            
            <!-- En-tête catégorie -->
            <tr class="category-header">
                <td colspan="5">
                    <strong><?= strtoupper(htmlspecialchars($cat->getType())) ?></strong>
                </td>
            </tr>
            
            <!-- Détails de la catégorie -->
            <?php foreach ($detailsByCategory[$cat->getId()] as $d): ?>
            <tr class="detail-row">
                <td>
                    <?= htmlspecialchars($d->getDescription() ?: 'Aucun détail') ?>
                </td>
                <td>
                    <?= date('d/m/Y', strtotime($d->getDateFrais())) ?>
                </td>
                <td class="text-right">
                    <?php if ($d->getMontantVeloce() > 0): ?>
                        <span class="text-success fw-bold"><?= number_format($d->getMontantVeloce(), 2) ?> €</span>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td class="text-right">
                    <?php if ($d->getMontantPersonnel() > 0): ?>
                        <span class="text-warning fw-bold"><?= number_format($d->getMontantPersonnel(), 2) ?> €</span>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td class="text-right fw-bold">
                    <?= number_format($d->getMontantVeloce() + $d->getMontantPersonnel(), 2) ?> €
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- TOTAUX -->
<div class="totals-section">
    <div class="total-row">
        <span class="total-label">Nombre de notes:</span>
        <span class="total-amount"><?= $totalNotes ?></span>
    </div>
    <div class="total-row">
        <span class="total-label">Total VÉLOCE:</span>
        <span class="total-amount"><?= number_format($grandTotalVeloce, 2) ?> €</span>
    </div>
    <div class="total-row">
        <span class="total-label">Total Personnel:</span>
        <span class="total-amount"><?= number_format($grandTotalPerso, 2) ?> €</span>
    </div>
    <div class="total-row">
        <span class="total-label">TOTAL GÉNÉRAL:</span>
        <span class="total-amount" style="font-size: 16pt;"><?= number_format($grandTotalVeloce + $grandTotalPerso, 2) ?> €</span>
    </div>
</div>

<!-- SIGNATURES -->
<div class="signatures">
    <div class="signature-box">
        <div>Signature de l'employé</div>
        <div class="signature-line"><?= htmlspecialchars($employe->getNom()) ?></div>
    </div>
    <div class="signature-box">
        <div>Signature du manager</div>
        <div class="signature-line">Validation requise</div>
    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    <p>Document généré automatiquement le <?= date('d/m/Y à H:i:s') ?></p>
    <p>VÉLOCE - Gestion des Notes de Frais - Confidentiel</p>
</div>

</body>
</html>
        <?php
        return ob_get_clean();
    }

    /**
     * Génère un PDF récapitulatif de tous les déplacements d'un utilisateur
     */
    public function generateUserReportPDF(User $user, array $deplacements): string
    {
        $html = $this->buildUserReportHTML($user, $deplacements);
        $this->mpdf->WriteHTML($html);
        return $this->mpdf->Output('', 'S');
    }

    /**
     * Construction HTML pour le rapport utilisateur
     */
    private function buildUserReportHTML(User $user, array $deplacements): string
    {
        // À implémenter selon vos besoins
        return '<h1>Rapport utilisateur</h1>';
    }
}