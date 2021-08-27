<?php
require_once('config/database.php');

class Connection
{
  private $conn = null;

  public function __construct()
  {
    $this->conn = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
  }

  // トランザクション開始
  public function beginTransaction()
  {
    $this->conn->beginTransaction();
  }

  // コミット
  public function commit()
  {
    $this->conn->commit();
  }

  // ロールバック
  public function rollback()
  {
    $this->conn->rollback();
  }

  // レコード取得用
  public function snapshot($sql)
  {
    $stmt = $this->conn->prepare($sql['sql']);
    foreach ($sql["params"] as $prm) {
      $stmt->bindValue($prm[0], $prm[1]);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return empty($result) ? null : $result;
  }

  // 単純にクエリ実行
  public function query($sql)
  {
    $stmt = $this->conn->prepare($sql["sql"]);
    foreach ($sql["params"] as $prm) {
      $stmt->bindValue($prm[0], $prm[1]);
    }
    $stmt->execute();
    return $this->conn->lastInsertId();
  }

  // トランザクションでクエリ実行(単体リソース)
  public function queryOnTransaction($sql)
  {
    try {
      $this->beginTransaction();
      $stmt = $this->conn->prepare($sql["sql"]);
      foreach ($sql["params"] as $prm) {
        $stmt->bindValue($prm[0], $prm[1]);
      }
      $stmt->execute();
      $this->commit();
    } catch (Exception $e) {
      $this->rollBack();
      echo "PDOException Occured." . $e->getMessage();
    }
  }
}
