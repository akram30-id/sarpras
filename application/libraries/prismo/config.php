<?php 
namespace Prismo;

class Config
{
	protected $CI;

	function __construct($CI)
	{
		$this->CI = $CI;
	}

	private function _selectField(array $params)
	{
		$selectField = [];
		foreach ($params['select'] as $key => $value) {
			if ($value == true) {
				$selectField[] = $value;
			}
		}
		$implodeSelectField = implode(', ', $selectField);

		$this->CI->db->select($implodeSelectField);
	}

	private function _whereField(array $params)
	{
		foreach ($params['where'] as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $whereKey => $whereValue) {
					if (is_array($whereValue)) {
						# code...
					}
				}
			}
			$this->db->where($key, $value);
		}
	}

	public function findFirst(String $table, array $params)
	{
		if ($params) {
			if (isset($params['select'])) {
				$this->_selectField($params);
			} else {
				$this->CI->db->select('*');
			}

			$this->CI->db->from($table);

			if (isset($params['where'])) {
				foreach ($params['where'] as $key => $value) {
					$this->db->where($key, $value);
				}
			}

			if (isset($params['orderBy'])) {
				$orderKey = key($params['orderBy']);
				$this->CI->db->order_by($orderKey, $params['orderBy'][$orderKey]);
			}

			$this->CI->db->limit(1);

			$result = $this->CI->db->get()->row();

			return $result;
		}
	}
}


?>
