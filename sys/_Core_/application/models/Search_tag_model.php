<?php

class Search_tag_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->_init();
	}

	/**
	 * 初期化関数
	 *
	 * @access	private
	 * @param
	 * @return
	 */
	private function _init() { }

    public function save ()
    {
        $this->db->empty_table('search_tag');
        $query = $this->db->from('rooms_data')->where(['output_flag' => 1, 'edit_id <>' => ''])->get();
        $insertData = [];
        if (!empty($query) && $query->num_rows() > 0) {
            $res = $query->result_array();
            foreach ($res as $row)
            {
                $insertData = $this->setSearchTagData($row, $insertData);
            }
        }
        $uniqueInsertData = $this->uniqueMultidimArray($insertData);

        if (count($uniqueInsertData) > 0) {
            $this->db->insert_batch('search_tag', $uniqueInsertData);
        }
        return false;
    }

    /**
     * @param $dataSet
     * @param $insertData
     * @return array
     */
    private function setSearchTagData ($dataSet, $insertData)
    {
        $keys = ['ldk', 'breadth', 'orientation', 'free_tag', 'price'];
        foreach ($dataSet as $key => $value) {
            if (array_search($key, $keys) !== false && empty($dataSet[$key] == false)) {
                $isFreeTag = false;
                switch ($key) {
                    case 'breadth':
                        $dataSet[$key] = floor(intval($dataSet[$key]) / 10) * 10;
                        break;
                    case 'price':
                        $dataSet[$key] = floor(intval($dataSet[$key]) / 1000) * 1000;
                        break;
                    case 'free_tag':
                        $freeTags = explode(',', $dataSet[$key]);
                        $insertTag = [];
                        foreach ($freeTags as $freeTag) {
                            $insertTag[] = ['tag_key' => $key, 'tag_value' => $freeTag];
                        }
                        $isFreeTag = true;
                        break;
                }
                if ($isFreeTag == false) {
                    $insertData[] = ['tag_key' => $key, 'tag_value' => $dataSet[$key]];
                } else {
                    $insertData = array_merge($insertData, $insertTag);
                }

            }
        }
        return $insertData;
    }

    /**
     * @param $targetArray
     * @return array
     */
    private function uniqueMultidimArray($targetArray)
    {
        $returnArray = [];
        foreach($targetArray as $val) {
            $foundCount = 0;
            if (count($returnArray) > 0) {
                foreach ($returnArray as $ta) {
                    if ($val['tag_key'] == $ta['tag_key'] && $val['tag_value'] == $ta['tag_value']) {
                        $foundCount++;
                    }
                }
            }

            if ($foundCount == 0) {
                $returnArray[] = $val;
            }
        }
        return $returnArray;
    }

    /**
     *
     */
    public function clear ()
    {
        $this->db->empty_table('search_tag');
    }

    /**
     * @return array
     */
    public function getTags ()
    {
        $query = $this->db->from('search_tag')->order_by('tag_value', 'ASC')->get();

        $ret = ['free_tag' => []];
        if (!empty($query) && $query->num_rows() > 0) {
            $res = $query->result_array();
            foreach ($res as $row)
            {
                if ($row['tag_key'] == 'free_tag') {
                    $ret[$row['tag_key']] = array_merge($ret[$row['tag_key']], explode(',', $row['tag_value']));
                } else {
                    $ret[$row['tag_key']][] = $row['tag_value'];
                }
            }
        }
        return $ret;
    }
}
