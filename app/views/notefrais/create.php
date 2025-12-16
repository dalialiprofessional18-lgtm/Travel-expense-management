<?php $title = 'Nouvelle note';
ob_start();
?>
<h1><i class = 'bi bi-file-earmark-plus'></i> Nouvelle note de frais</h1>
<p class = 'text-muted'>Déplacement : <strong><?= htmlspecialchars( $deplacement->getTitre() ) ?></strong></p>

<div class = 'card shadow'>
<div class = 'card-body'>
<form action = '/détails/store' method = 'POST'>
<input type = 'hidden' name = 'deplacement_id' value = "<?= $deplacement->getId() ?>">
<p class = 'text-center py-5 text-muted'>
Une note de frais vide va être créée.<br>
Vous pourrez ensuite ajouter les détails ( repas, hôtel, etc. ).
</p>
<div class = 'text-center'>
<button type = 'submit' class = 'btn btn-success btn-lg'><i class = 'bi bi-plus-circle'></i> Créer la note</button>
<a href = "/notes/<?= $deplacement->getId() ?>" class = 'btn btn-secondary btn-lg'>Retour</a>
</div>
</form>
</div>
</div>
