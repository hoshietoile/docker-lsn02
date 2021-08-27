<?php
require_once('app/connection.php');

class Controller
{
  private $db = null;

  public function __construct()
  {
    $this->db = new Connection();
  }

  // 分析文字列保存
  public function storeSentence()
  {
    $row_request = $_POST;
    $row_request['words'] = json_decode($row_request['words'], true);
    $request = $this->_sanitize($row_request);
    $sentence = $request['sentence'];
    $words = $request['words'];

    try {
      $this->db->beginTransaction();
      $sql_for_sentence = $this->_getSqlForInsertSentence($sentence);
      $sentence_id = $this->db->query($sql_for_sentence);
      $sql_for_words = $this->_getSqlForInsertWords($sentence_id, $words);
      $this->db->query($sql_for_words);
      $this->db->commit();
    } catch (Exception $e) {
      $this->db->rollback();
      echo "PDOException occured." . $e->getMessage();
    }
    $this->_response($sentence_id);
  }

  // 分析文字列リスト取得
  public function getSentenceList()
  {
    $sql = $this->_getSqlForSelect();
    $result = $this->db->snapshot($sql);
    return $this->_response($result);
  }

  // ワード一覧取得
  public function getWordList()
  {
    $request = $this->_sanitize($_POST);
    $sql = $this->_getSqlForWords($request);
    $result = $this->db->snapshot($sql);
    return $this->_response($result);
  }

  // 品詞一覧取得
  public function getPosList()
  {
    $request = $this->_sanitize($_POST);
    $sql = $this->_getSqlForPos($request);
    $result = $this->db->snapshot($sql);
    return $this->_response($result);
  }

  // wordsフィルタリング用where句生成関数
  private function _getWordWhereClause($req)
  {
    $whereClause = [];
    $whereParams = [];
    $whereQuery = "";
    if (!empty($req['surface'])) {
      $value = $req['surface'];
      $whereClause[] = "surface_form like :surface";
      $whereParams[] = [':surface', "%${value}%"];
    }
    if (!empty($req['pos'])) {
      $value = $req['pos'];
      $whereClause[] = "pos = :pos";
      $whereParams[] = [':pos', $value];
    }
    if (!empty($req['pronunciation'])) {
      $value = $req['pronunciation'];
      $whereClause[] = "pronunciation = :pronunciation";
      $whereParams[] = [':pronunciation', $value];
    }
    if (count($whereClause) > 0 && count($whereParams) > 0) {
      $whereQuery = "WHERE " . implode(" AND ", $whereClause);
    }
    return [$whereQuery, $whereParams];
  }

  // ワード一覧取得用SQL
  private function _getSqlForWords($req)
  {
    list($whereQuery, $whereParams) = $this->_getWordWhereClause($req);

    $sql = <<<EOT
      SELECT surface_form, count(1) as cnt
      FROM words
      ${whereQuery}
      group by surface_form
      order by cnt desc
      limit 10
    EOT;
    return ['sql' => $sql, 'params' => $whereParams];
  }

  // sentences一覧取得用SQL
  private function _getSqlForSelect()
  {
    $sql = <<<EOT
      SELECT *
      FROM sentences
    EOT;
    return ['sql' => $sql];
  }

  // 品詞一覧取得用SQL
  private function _getSqlForPos($req)
  {
    list($whereQuery, $whereParams) = $this->_getWordWhereClause($req);

    $sql = <<<EOT
      SELECT pos, COUNT(1) AS cnt
      FROM words
      ${whereQuery}
      GROUP BY pos
      ORDER BY cnt DESC
    EOT;
    return ['sql' => $sql, 'params' => $whereParams];
  }

  // sentences用のインサート文
  private function _getSqlForInsertSentence($resource)
  {
    $sql = <<<EOT
      INSERT INTO sentences
      (sentence)
      VALUES
      (:sentence);
    EOT;
    $params = array(
      [':sentence', $resource],
    );
    return ['sql' => $sql, 'params' => $params];
  }

  // words用のインサート文
  private function _getSqlForInsertWords($sentence_id, $insert_data)
  {
    if (empty($insert_data)) {
      return null;
    }

    // カラムリスト
    $columns = array(
      'sentence_id' => ':sentence_id',
      'word_id' => ':word_id',
      'surface_form' => ':surface_form',
      'basic_form' => ':basic_form',
      'conjugated_type' => ':conjugated_type',
      'pos' => ':pos',
      'pronunciation' => ':pronunciation',
      'reading' => ':reading',
    );
    $keys = array_keys($columns);

    $results = array_reduce(array_keys($insert_data), function ($acc, $idx) use ($insert_data, $columns, $keys, $sentence_id) {
      $cur_item = $insert_data[$idx];
      if (empty($cur_item)) {
        return $acc;
      }

      // キーバインディング用の各キーに識別用のインデックスを配布
      $values = array_map(function ($val) use ($idx) {
        return $val . $idx;
      }, $columns);

      // 共通データの挿入
      $cur_item['sentence_id'] = $sentence_id;
      foreach ($keys as $key) {
        $insert_key = $columns[$key] . $idx;
        $value = !empty($cur_item[$key]) ? $cur_item[$key] : null;
        $acc['params'][] = [$insert_key, $value];
      }
      $acc['values'][] = '(' . implode(', ', $values) . ')';
      return $acc;
    }, array('values' => [], 'params' => []));

    // sql文の成型
    $value_list = implode(', ', $results['values']);
    $sql = <<<EOT
      INSERT INTO words
      (sentence_id, word_id, surface_form, basic_form, conjugated_type, pos, pronunciation, reading)
      VALUES
      ${value_list};
    EOT;

    return ['sql' => $sql, 'params' => $results['params']];
  }

  // 以下とりあえずここら辺に置いとく

  // [xss対策]配列内を再帰的にエスケープ
  private function _sanitize($target)
  {
    return filter_var($target, FILTER_CALLBACK, array('options' => fn ($t) => htmlspecialchars($t)));
  }

  // json_responseを返す
  private function _response($data)
  {
    print json_encode($data, JSON_PRETTY_PRINT);
  }
}
