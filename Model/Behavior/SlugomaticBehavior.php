<?php

App::uses('Inflector', 'Utility');
App::uses('Hash', 'Utility');

class SlugomaticBehavior extends ModelBehavior {

	private $__settings = array();

/**
 * Behaviour setup
 *
 * @param Model $model The current model being used
 * @param array $settings Setup array with options
 *
 * @return void
 */
	public function setup(Model $model, $settings = array()) {
		$default = array(
			'fields' => 'title',
			'scope' => false,
			'conditions' => false,
			'slugfield' => 'slug',
			'separator' => '-',
			'overwrite' => false,
			'length' => 256,
			'lower' => true
		);

		$this->__settings[$model->alias] = (!empty($settings)) ? $settings + $default : $default;
	}

/**
 * beforeSave callback
 *
 * @param Model $model The current model being used
 * @param array $options Callback options
 *
 * @return mixed
 */
	public function beforeSave(Model $model, $options = array()) {
		$fields = (array)$this->__settings[$model->alias]['fields'];
		$scope = (array)$this->__settings[$model->alias]['scope'];
		$conditions = !empty($this->__settings[$model->alias]['conditions']) ? (array)$this->__settings[$model->alias]['conditions'] : array();
		$slugfield = $this->__settings[$model->alias]['slugfield'];
		$hasFields = true;

		foreach ($fields as $field) {
			if (!$model->hasField($field)) {
				$hasFields = false;
			}

			if (!isset($model->data[$model->alias][$field])) {
				$hasFields = false;
			}
		}

		if ($hasFields && $model->hasField($slugfield) && ($this->__settings[$model->alias]['overwrite'] || empty($model->id))) {
			$toSlug = array();

			foreach ($fields as $field) {
				$toSlug[] = $model->data[$model->alias][$field];
			}

			$toSlug = join(' ', $toSlug);

			$slug = Inflector::slug($toSlug, $this->__settings[$model->alias]['separator']);

			if ($this->__settings[$model->alias]['lower']) {
				$slug = strtolower($slug);
			}

			if (strlen($slug) > $this->__settings[$model->alias]['length']) {
				$slug = substr($slug, 0, $this->__settings[$model->alias]['length']);
			}

			$conditions[$model->alias . '.' . $slugfield . ' LIKE'] = $slug . '%';

			if (!empty($model->id)) {
				$conditions[$model->alias . '.' . $model->primaryKey . ' !='] = $model->id;
			}

			if (!empty($scope)) {
				foreach ($scope as $s) {
					if (isset($model->data[$model->alias][$s])) {
						$conditions[$model->alias . '.' . $s] = $model->data[$model->alias][$s];
					}
				}
			}

			$sameUrls = $model->find('all', array(
				'recursive' => -1,
				'conditions' => $conditions
			));

			$sameUrls = (!empty($sameUrls))
				? Hash::extract($sameUrls, '{n}.' . $model->alias . '.' . $slugfield)
				: array();

			if (!empty($sameUrls)) {
				if (in_array($slug, $sameUrls)) {
					$begginingSlug = $slug;
					$index = 1;

					while ($index > 0) {
						if (!in_array($begginingSlug . $this->__settings[$model->alias]['separator'] . $index, $sameUrls)) {
							$slug = $begginingSlug . $this->__settings[$model->alias]['separator'] . $index;
							$index = -1;
						}

						$index++;
					}
				}
			}

			$model->data[$model->alias][$slugfield] = $slug;
		}

		return parent::beforeSave($model);
	}
}