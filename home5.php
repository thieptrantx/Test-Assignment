<h1>Search Improvement</h1>
<hr />
<p>Could you please check the code below and improve the search function in Model</p>
<?php

/**
 * Creates data provider instance with search query applied
 *
 * @param array $params
 * @return ActiveDataProvider
 */
public function search($params, $export = false) {
    $query = Post::find()
        ->from(Post::tableName() . ' t')
        ->joinWith(['parent' => function ($q) {
        $q->from(Post::tableName() . ' p');
    }]);

    // add conditions that should always apply here
    $arrData = ['query' => $query];
    if(!empty($params['PostSearch']['filters']) || $export) {
        $arrData['pagination'] = false;
    }
    $dataProvider = new ActiveDataProvider($arrData);

    $sort = $dataProvider->getSort();
    $sort->attributes['parent.name'] = [
        'asc' => ['p.name' => SORT_ASC],
        'desc' => ['p.name' => SORT_DESC],
        'label' => 'p',
    ];
    $sort->attributes['ordering'] = [
        'asc' => ['p.ordering' => SORT_ASC, 't.ordering' => SORT_ASC],
        'desc' => ['p.ordering' => SORT_DESC, 't.ordering' => SORT_DESC],
        'label' => 'order',
    ];
    $sort->defaultOrder = ['updated_at' => SORT_DESC];
    
    $this->load($params);

    if (!$this->validate()) {
        // uncomment the following line if you do not want to return any records when validation fails
        $query->where('0=1');
    }

    $endDate = ($params['PostSearch']['endDate']) ?? date('Y-m-d H:i', time());
    $endDate = strtotime($endDate);
    if(!empty($params['PostSearch']['time_window']) || !empty($params['PostSearch']['endDate'])) {
        
        $query->innerJoin(['pa' => PostAnalytic::tableName()], 't.id = pa.post_id');

        $timeWindow = ($params['PostSearch']['time_window']) ?? 0;
        if($timeWindow) {
            $startDate = strtotime("- $timeWindow days", $endDate);
            $startDate = date('Y-m-d 00:00:00', $startDate);
            $startDate = strtotime($startDate);
            $query->andWhere(['>=', 'pa.view_date', $startDate]);
        }
        $query->andWhere(['<=', 'pa.view_date', $endDate]);

        //last updated
        $lastUpdated = ($params['PostSearch']['last_updated']) ?? 0;
        if($lastUpdated) {
            $query->andWhere(['>=', "DATEDIFF('".date('Y-m-d', $endDate)."', DATE_FORMAT(FROM_UNIXTIME(t.updated_at), '%Y-%m-%d'))", $lastUpdated]);
        }

        //region
        $region = ($params['PostSearch']['region']) ?? '';
        if($region) {
            $query->andFilterWhere(['like', 'pa.regions', $region]);
        }

        //tags
        $tags = ($params['PostSearch']['tags']) ?? [];
        if($tags) {
            $query->andFilterWhere([
                't.id' => new Query([
                    'select'  => ['cr.content_id'],
                    'from'    => ['cr' => 'rsc_category_relation'],
                    'where'   => ['cr.category_id' => $tags],
                    'groupBy' => ['cr.content_id'],
                    'having'  => 'COUNT(*)=' . count($tags),
                ]),
            ]);
        }

        //product_tags
        $product_tags = ($params['PostSearch']['product_tags']) ?? [];
        if($product_tags) {
            $query->andFilterWhere([
                't.id' => new Query([
                    'select'  => ['cr.content_id'],
                    'from'    => ['cr' => 'rsc_category_relation'],
                    'where'   => ['cr.category_id' => $product_tags],
                    'groupBy' => ['cr.content_id'],
                    'having'  => 'COUNT(*)=' . count($product_tags),
                ]),
            ]);
        }

        $query->groupBy(['pa.post_id']);
    }

    //apply list filters
    if(!empty($params['PostSearch']['filters'])) {
        $conditions = ['greater' => '>=', 'smaller' => '<='];

        $query->innerJoin(['pa' => PostAnalytic::tableName()], 't.id = pa.post_id');
        $query->leftJoin(['m' => \trdx\cms\models\PostMeta::tableName()], "m.post_id = t.id AND m.meta_key = 'params'");

        $filters = \trdx\cms\models\FilterManagement::find()->where(['status' => 1])->all();
        $arrCondition = [];
        foreach($filters as $item) {
            $cond_hit_time = isset($item->params['cond_hit_time']) ? $conditions[$item->params['cond_hit_time']] : '>=';
            $cond_update_time = isset($item->params['cond_update_time']) ? $conditions[$item->params['cond_update_time']] : '>=';

            $tags = ($item->tags) ?? [];
            $condTags = [];
            if($tags) {
                $condTags = ['t.id' => new Query([
                    'select'  => ['cr.content_id'],
                    'from'    => ['cr' => 'rsc_category_relation'],
                    'where'   => ['cr.category_id' => $tags],
                    'groupBy' => ['cr.content_id'],
                    'having'  => 'COUNT(*)=' . count($tags),
                ])];
            }

            $productTags = ($item->product_tags) ?? [];
            $condProductTags = [];
            if($productTags) {
                $condProductTags = ['t.id' => new Query([
                    'select'  => ['cr.content_id'],
                    'from'    => ['cr' => 'rsc_category_relation'],
                    'where'   => ['cr.category_id' => $productTags],
                    'groupBy' => ['cr.content_id'],
                    'having'  => 'COUNT(*)=' . count($productTags),
                ])];
            }

            $region = [];
            if($item->region) {
                $region = ['like', 'pa.regions', $item->region];
            }

            $priority = ['OR', 
                ['m.meta_data' => new \yii\db\Expression('Null')],
                ['NOT LIKE', 'm.meta_data', '"update_priority"'],
                ['LIKE', 'm.meta_data', '"update_priority":"auto"'],
            ];
            //if priority is auto then filter for all priorities
            if($item->priority != 'auto') {
                $priority = ['like', 'm.meta_data', '"update_priority":"'.$item->priority.'"'];
            }

            $reminder = ['OR', 
                ['m.meta_data' => new \yii\db\Expression('Null')],
                ['NOT LIKE', 'm.meta_data', '"update_reminder"'],
                ['LIKE', 'm.meta_data', '"update_reminder":"1"'],
            ];
            
            $query->orFilterWhere([
                'AND',
                [$cond_hit_time, 't.view_count', $item->hit_time],
                [$cond_update_time, "DATEDIFF('".date('Y-m-d', $endDate)."', DATE_FORMAT(FROM_UNIXTIME(t.updated_at), '%Y-%m-%d'))", $item->update_time],
                $region,
                $priority,
                $condTags,
                $condProductTags,
                $reminder,
            ]);
        }
        $query->groupBy(['pa.post_id']);
        $query->andFilterWhere(['!=', 't.parent_id', 100150]);
    }

    // filter type
    if(in_array($this->initType, Post::singlePostTypes())) {
        $query->andFilterWhere(['t.type' => $this->initType]);
    } else {
        $query->andFilterWhere(['t.type' => array_keys(Post::typeOptions())]);
    }

    // filter status
    $query->status($this->status, false, 't');
    $query->andFilterCompare('t.ordering', $this->ordering);

    // grid filtering conditions
    $query->andFilterWhere([
        't.id' => $this->id,
        't.parent_id' => $this->parent_id,
        't.published' => $this->published,
        't.type' => $this->type,
        't.site' => Yii::$app->id,
    ]);

    $query->andFilterWhere(['like', 't.name', $this->name])
            ->andFilterWhere(['like', 't.content_text', $this->content_text]);

    // and with user role - conditions
    if($this->userCondition) {
        $query->andWhere($this->userCondition);
    }

    if($export) {
        $query->orderBy(['updated_at' => SORT_DESC]);
        return $query->all();
    }

    return $dataProvider;
}