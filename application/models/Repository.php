<?php


class Repository extends CI_Model
{

	public function findFirst($table, $where, $select = '*')
	{
		$this->db->select($select);
		$query = $this->db->get_where($table, $where)->first_row();

		return $query;
	}

	public function findMany($table, $where, $select = '*')
	{
		$this->db->select($select);
		$query = $this->db->get_where($table, $where)->result();

		return $query;
	}

	public function delete($table, $where)
	{
		$this->db->where($where);
		$query = $this->db->delete($table);

		return $query;
	}

	public function create($table, $data)
	{
		$save = $this->db->insert($table, $data);

		return $save;
	}

}
