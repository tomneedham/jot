<?php

// Include the scripts
\OCP\Util::addScript('jot', 'isotope.pkgd.min');
\OCP\Util::addScript('jot', 'expanding');
\OCP\Util::addScript('jot', 'app');
\OCP\Util::addScript('jot', 'dropzone');
\OCP\Util::addScript('jot', 'item');

// Include the styles
\OCP\Util::addStyle('jot', 'main');
\OCP\Util::addStyle('jot', 'responsive');

?>

<div id="app">
	<div id="app-content">
		<button class="jot-import">Import from Google Keep</button>
		<?php print_unescaped($this->inc("part.cards")); ?>
	</div>
</div>
