<?php
class ArticlesController extends AppController {

	function index($lang = null) {

		if ($lang) {
			$this->Session->write('lang', $lang);
		} else {
			if ($this->Session->read('lang')) {
				$lang = $this->Session->read('lang');
			} else {
				$lang = 'es';
				$this->Session->write('lang', $lang);
			}
		}

		$articles = $this->Article->find('all', array(
			'conditions' => array('lang' => $lang),
			'order' => array('week' => 'asc', 'order' => 'asc', 'category' => 'asc'),
		));

		$this->set(compact('articles'));

	}

	function edit($id = null) {

		if ($this->request->data) {

			if ($id) {
				$this->Article->id = $id;
			} else {
				$this->Article->create();
			}

			if (!empty($this->request->data['Article']['parent_id'])) {
				$parent = $this->Article->findById($this->request->data['Article']['parent_id']);
				$this->request->data['Article']['order'] = $parent['Article']['order'];
			} else {
				if ($id) {
					$sons = $this->Article->find('all', array(
						'conditions' => array(
							'parent_id' => $id,
						),
					));

					$this->Article->updateAll(array('order' => $this->request->data['Article']['order']), array('parent_id' => $id));

				}
				$this->request->data['Article']['parent_id'] = null;
			}

			unset($this->request->data['Article']['image']);

			$this->Article->save($this->request->data);

			if (!$id) {
				$id = $this->Article->id;
			}

			if (!empty($_FILES['data']['tmp_name']['Article']['image'])) {
				$ext = explode('.', $_FILES['data']['name']['Article']['image']);
				$ext = '.' . $ext[count($ext) - 1];
				move_uploaded_file($_FILES['data']['tmp_name']['Article']['image'], WWW_ROOT . 'images' . DS . $id . $ext);
			}

			return $this->redirect('index');

		}

		if ($id) {

			$this->request->data = $this->Article->findById($id);

		} else {

			$this->request->data['Article']['lang'] = $this->Session->read('lang');

		}

		$articles = $this->Article->find('all', array(
			'conditions' => array(
				'lang' => $this->Session->read('lang'),
				'parent_id' => null,
			),
			'fields' => array('id', 'title', 'header'),
			'order' => array('week' => 'asc', 'order' => 'asc', 'category' => 'asc'),
		));

		$parents = array();

		foreach ($articles as $article) {
			extract($article);
			if (!empty($Article['header'])) {
				if (!empty($header)) {
					$parents[$header] = $array;
				}
				$header = $Article['header'];
				$array = array($Article['id'] => $Article['title']);
			} else {
				$array[$Article['id']] = $Article['title'];
			}

		}
		$parents[$header] = $array;

		$this->set(compact('id', 'parents'));

	}

	function delete($id = null) {

		if ($id) {

			$this->Article->delete($id);

			return $this->redirect(array('controller' => 'articles', 'action' => 'index'));

		}

	}

	function json($lang = null) {

		if (!$lang) $lang = 'es';

		$conditions = array(
			'lang' => $lang,
		);

		$onlyTips = false;

		if (!empty($this->request->data['onlyTips'])) {
			//$conditions['category'] = array('tips_mom', 'tips_dad');
			$onlyTips = true;
		}

		if (!empty($this->request->data['pay']) && $this->request->data['pay'] == true) {
			$pay = true;
		} else {
			$pay = false;
		}
		
		$articles = $this->Article->find('all', array(
			'conditions' => $conditions,
			'order' => array('week' => 'asc', 'order' => 'asc', 'category' => 'asc'),
		));

		$return = array();

		$aux_header = $aux = null;

		foreach ($articles as $article) {

			extract($article);

			$Article['image'] = 'http://api.elembarazo.net/images/' . $Article['id'] . '.jpg';
			$Article['last'] = false;

			if (!empty($Article['header']) && $aux) {
				$aux['last'] = true;
			}

			if ($onlyTips) {
				if ($Article['category'] == 'tips_dad' || $Article['category'] == 'tips_mom') {
					if ($aux_header) {
						$Article['header'] = $aux_header;
					}
					$aux_header = null;
				} else {
					if ($Article['header']) {
						$aux_header = $Article['header'];
					}
					continue;
				}
			}

			if (!empty($Article['urls'])) {
				$urls = $Article['urls'];
				$urls = explode("\r\n", $urls);

				$i = 0;
				$Article['urls'] = array();
				foreach ($urls as $url) {
					if ($i %2 == 0) {
						$aux_url = $url;
					} else {
						$Article['urls'][] = array('title' => trim($aux_url), 'url' => trim($url));
					}
					$i ++;
				}
			}

			if ($aux) {
				$return[] = $aux;
			}

			$aux = $Article;

		}
		$aux['last'] = true;
		$return[] = $aux;

		$this->layout = false;

		$this->set(compact('return', 'pay'));

	}

	function getData() {

		return;

		$ch = curl_init('http://www.semanasdembarazo.com/appMovil/weeks.php');
		$lang = 'es';
		//$ch = curl_init('api.lagravidanza.net/posts/index.json');
		//$lang = 'it';
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$res = json_decode(curl_exec($ch));

		if ($res->status === 'ok') {

			$parent_id = null;

			$order = 0;

			foreach ($res->data as $article) {

				$order ++;

				if ($article->category === 'tip') {

					if ($article->post_type === 'consejos') {

						$category = 'tips_mom';
						
					} else {
						
						$category = 'tips_dad';

					}

				} else {

					$category = $article->category;

				}

				if ($category == 'mom' || $category == 'baby' || $category == 'general') {
					$parent_id = null;
				}

				$to_save = array(
					'title' => $article->title,
					'intro' => $article->intro,
					'text' => $article->description,
					'category' => $category,
					'header' => !empty($article->header) ? $article->header : null,
					'image' => !empty($article->image) ? $article->image : null,
					'parent_id' => $parent_id,
					'order' => $order,
					'lang' => $lang,
					'external_id' => $article->ID,
				);

				if ($aux = $this->Article->find('first', array(
					'conditions' => array(
						'title' => $article->title,
						'intro' => $article->intro,
						'text' => $article->description,
						'category' => $category,
						'header' => !empty($article->header) ? $article->header : null,
						'image' => !empty($article->image) ? $article->image : null,
						'parent_id' => $parent_id,
					)
				))) {
					$this->Article->id = $aux['Article']['id'];
				} else {
					$this->Article->create();
				}

				$this->Article->save($to_save);

				if ($category == 'mom' || $category == 'baby') {
					$parent_id = $this->Article->id;
				}

			}

		} else {

			echo 'error';

		}

		die;

	}

	function getImages() {

		return;

		$articles = $this->Article->find('all', array(
			'conditions' => array(
				//'lang' => 'it'
			)
		));

		if (!is_dir(WWW_ROOT . 'images')) {
			mkdir(WWW_ROOT . 'images');
			die;
		}

		foreach ($articles as $article) {

			extract($article);

			$image = str_replace('-150x150', '', $Article['image']);

			$ext = explode('.', $image);
			$ext = '.' . $ext[count($ext) - 1];

			$ch = curl_init($image);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$res = curl_exec($ch);

			$file = fopen(WWW_ROOT . 'images' . DS . $Article['id'] . $ext, 'w');
			fwrite($file, $res);
			fclose($file);

		}

		die;

	}

}
