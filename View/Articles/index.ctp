<?php

echo $this->Html->link(__('Crear nuevo artículo', true), array('controller' => 'articles', 'action' => 'edit'));

echo '<ul>';

foreach ($articles as $article) {

	extract($article);

	echo (!empty($Article['header']) ? ' (' . $Article['header'] . ')' : '');

	echo '<li>' . (!empty($Article['parent_id']) ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . $this->Html->link($Article['title'], array('controller' => 'articles', 'action' => 'edit', $Article['id'])) . '</li>';

}

echo '</ul>';
