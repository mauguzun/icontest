
<? if ($feed) : ?>

<? foreach($feed->channel->item  as $item): ?>
	
	<article>
		
		<h3><?= $item->title ?></h3>

		<p><?= $item->description ?></p>
		<a  href="<?= $item->link ?>" target="_blank" class="btn btn-primary" ><?= lang('view_more')?></a>
	</article>

<? endforeach ?>

<? endif;?>