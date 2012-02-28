<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';

/*
 * This class implements all default behaviors of model. The extended class
 * only need to redefine the abnormal behaviors.
 */
abstract class Ent_model extends CI_Model {

  public function __construct() {
    parent::__construct();
  }

  /*
   * The following functions are to be defined in each extended class.
   */
  abstract protected function tableName();
  abstract protected function typeName();

  /*
   * Redefine if needed.
   */
  protected function insertValidationFunc() {
    return null;
  }
  protected function insertBlacklist() {
    return array();
  }

  // this function is purely temporary and nosense.
  public function get_id_list($limit = 100, $offset = 0) {
    $type = $this->typeName();
    $result_list = array();
    $query = $this->db->get($this->tableName(), $limit, $offset);
    foreach ($query->result_array() as $row) {
      $ent = new $type();
      $ent->load_array($row);
      $result_list[] = $ent;
    }
    $this->db->flush_cache();
    return $result_list;
  }

  /*
   * Get Entities with id(s).
   * For any $id not in the database, $result_list[$id] = NOT_SET;
   */
  public function get_ents($ids) {
    if (!is_array($ids)) {
      $ids = array($ids);
    }
    $type = $this->typeName();
    $result_list = array_combine($ids, array_fill(0, count($ids), NOT_SET));
    $this->db->where_in('sid', $ids);
    $query = $this->db->get($this->tableName());
    foreach ($query->result_array() as $row) {
      $ent = new $type();
      $ent->load_array($row)->unzip();
      $result_list[$ent->get('sid')] = $ent;
    }
    $this->db->flush_cache();
    return $result_list;
  }

  /*
  * Delete Entities with id(s).
  * if $with_report is set 'true', it returns an array like:
  *   array( id1 => result, id2 => result, ... )
  * The valid results are:
  *   ('deleted', 'not_existed')
  */
  public function delete($ids, $with_report = false) {
    if (!is_array($ids)) {
      $ids = array($ids);
    }
    // generate the report.
    if ($with_report) {
      $report = array_combine($ids, array_fill(0, count($ids), 'not_existed'));
      $this->db->where_in('sid', $ids);
      $this->db->select('sid');
      $query = $this->db->get($this->tableName());
      foreach ($query->result_array() as $row) {
        // all existing entries are going to be deleted.
        $report[$row['sid']] = 'deleted';
      }
      // this is required because the 'where_in' ad 'select' will be kept in
      // the cache.
      $this->db->flush_cache();
    }

    // perform the deletion.
    $this->db->where_in('sid', $ids);
    $query = $this->db->delete($this->tableName());
    $this->db->flush_cache();

    if ($with_report) {
      return $report;
    }
  }

  /*
   * Insert some new entry(s) to database.
   * it returns an array like:
   *   array( id1 => result, id2 => result, ... )
   * The valid results are:
   *   ('inserted', 'covered', 'failed : XXXX')
   * for the ent even not an Ent, just drop it silently.
   */
  public function insert($ents) {
    if (!is_array($ents)) {
      $ents = array($ents);
    }
    $type = $this->typeName();
    $validFunc = $this->insertValidationFunc();
    $report = array();

    foreach ($ents as $ent) {
      // check Ent.
      if (!($ent instanceof Ent)) continue;

      // check if the subtype of Ent matches.
      if (!($ent instanceof $type)) {
        $report[$ent->get('sid')] = 'failed : Not valid instance.';
        continue;
      }

      $ent->zip();

      // check validation.
      if ($this->insertValidationFunc() != null && !$ent->$validFunc()) {
        $report[$ent->get('sid')] = 'failed : Not passing validation.';
        continue;
      }
      // test if exists.
      $this->db->where('sid', $ent->get('sid'));
      $this->db->from($this->tableName());
      if ($this->db->count_all_results() > 0) {
        $this->db->delete($this->tableName());
        $report[$ent->get('sid')] = 'deleted';
      }
      $this->db->flush_cache();

      // perform the insert.
      $entry = $ent->to_array($zipped = true, $this->insertBlacklist(),
                              'blacklist', $check_empty = true);
      $query = $this->db->insert($this->tableName(), $entry);
      $this->db->flush_cache();
      // generate the report.
      if (isset($report[$ent->get('sid')]) &&
      	  ($report[$ent->get('sid')]  == 'deleted')) {
        $report[$ent->get('sid')] = 'covered';
      } else {
        $report[$ent->get('sid')] = 'inserted';
      }
    }
    return $report;
  }

  /*
   * Update some entry(s) in the database using sid as key.
   * if $with_report is set 'true', it returns an array like:
   *   array( id1 => result, id2 => result, ... );
   * The valid results are:
   *   ('updated', 'inserted', 'failed : XXX')
   * The boolean $insert controls whether insert the ent when not existing.
   */
  public function update($ents, $with_report = false, $insert = true) {
    // todo: add code to complete this function.
    //      reminder: validate before insert.
  }
}

/* End of file ent_model.php */
/* Location: ./application/models/ent/ent_model.php */