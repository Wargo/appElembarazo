<?php

echo $this->Html->link(__('Volver', true), array('controller' => 'articles', 'action' => 'index'));

echo $this->Form->create('Article', array('type' => 'file', 'url' => array('controller' => 'articles', 'action' => 'edit', $id)));

echo $this->Form->inputs(array(

	'fieldset' => false,

	'title' => array(
		'label' => __('Título', true),
	),

	'header' => array(
		'label' => __('Cabecera', true),
	),

	'intro' => array(
		'label' => __('Introducción', true),
	),

	'text' => array(
		'label' => __('Texto', true),
	),

	'category' => array(
		'label' => __('Categoría', true),
		'options' => array(
			'mom' => __('Mamá'),
			'baby' => __('Bebé'),
			'general' => __('General'),
			'tips_mom' => __('Consejos mamá'),
			'tips_dad' => __('Consejos papá'),
			'fruit' => __('Fruta'),
		),
	),

	'order' => array(
		'label' => __('Orden', true),
	),

	'lang' => array(
		'label' => __('Idioma', true),
		'options' => array(
			'es' => __('Español', true),
			'it' => __('Italiano', true),
			'en' => __('Inglés', true),
			'ge' => __('Alemán', true),
			'pt' => __('Portugués', true),
		)
	),

	'parent_id' => array(
		'label' => __('Artículo padre', true),
		'type' => 'text',
	),

	'image' => array(
		'label' => __('Imagen', true),
		'type' => 'file',
	),

));

if ($id) {
	//echo $this->Html->image($this->request->data['Article']['image'], array('width' => 200));
	echo $this->Html->image('/images/' . $id . '.jpg?rnd=' . mt_rand());
}

echo $this->Form->inputs(array(
	'fieldset' => false,
	'urls' => array(
		'label' => __('Enlaces', true),
		'after' => __('Ejemplos:<br />Enlace a Google<br />http://www.google.es<br />Enlace a Facebook<br />http://www.facebook.com', true)
	),
));

echo $this->Html->link(__('Añadir más enlaces', true), array('controller' => 'articles', 'action' => 'add_link'), array('style' => 'float: right;'));

echo $this->Form->end(__('Guardar', true));
echo $this->Html->link(__('Cancelar', true), array('controller' => 'articles', 'action' => 'index'));
