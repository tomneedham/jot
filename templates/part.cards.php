<div class="jot-wrapper">
	<div id="jot-items" style="" data-count=<?php p(count($_['jots'])); ?> class="jot-items">
		<!-- Start items-->
		<div class="jot-item add-jot-item" data-mtime=999999999999999 style="position: absolute; left: 0px; top: 0px;"><div class="jot-item-content" style="padding-bottom: 5px;height: 80px;"><span class="icon-add" style="
    height: 25px;
    /* width: 49px; */
    display: block;
    margin: auto auto;
    opacity: 0.2;
    background-size: 25px;
    margin-top: 13px;
"></span></div></div>
		<?php foreach($_['jots'] as $jot) { ?>
		<div class="jot-item" data-mtime="<?php p($jot->getMTime()); ?>" data-id="<?php p($jot->getId()); ?>" data-type="text">
			<div class="jot-item-content" style="padding-bottom: 5px;">
				<a class="item-state-icon icon-close"></a>
				<input type="text" name="title" class="jot-input jot-title" placeholder="Title" value="<?php p($jot->getTitle()); ?>"></input>
				<textarea class="jot-input jot-content" placeholder="..." rows=1 style=""><?php p($jot->getContent()); ?></textarea>
				<div class="jot-item-images dropzone-previews">
					<?php foreach($jot->getImages() as $id => $url) { ?>
						<img width=70 height=70 src="<?php p($url); ?>" alt="<?php p($id); ?>"></img>
					<?php } ?>
				</div>
				<p class="modified"></p>
			</div>
		</div>
		<?php } ?>
		<!-- End items -->
	</div>
</div>
