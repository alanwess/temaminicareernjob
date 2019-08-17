<?php
	/**
	Template Name: Search Form
	**/
?>

<form role="search" method="get" class="header-job-search" action="<?php echo home_url( '/' ); ?>">
    <div class="input-keyword">
    	<input class="form-control" type="text" id="s" name="s" value="<?php echo get_search_query() ?>" placeholder="Vaga. Ex: Mecânico...">
    </div>
    <?php if ($_REQUEST['localidade']) $localidade = $_REQUEST['localidade']; ?>
    <div class="input-location">
      	<input id="localidade" name="localidade" type="text" class="form-control" value="<?= $localidade; ?>" placeholder="Cidade">
    </div>
    <div class="btn-search">
    	<button type="submit" class="btn btn-primary">Procurar vagas</button>
	</div>
</form>