<?php
/**
 * Very basic class to use pdo in a singleton
 */
class pdo55 {
  /**
   * Instance de la classe PDO
   *
   * @var PDO
   * @access private
   */
  private $PDOInstance = null;
   
  /**
   * Instance de la classe
   *
   * @var 
   * @access private
   * @static
   */
  private static $instance = null;
   
  /**
   * Constante: nom d'utilisateur de la bdd
   *
   * @var string
   */
  const DEFAULT_SQL_USER = 'root';
   
  /**
   * Constante: hôte de la bdd
   *
   * @var string
   */
  const DEFAULT_SQL_HOST = 'localhost';
   
  /**
   * Constante: hôte de la bdd
   *
   * @var string
   */
  const DEFAULT_SQL_PASS = '';
   
  /**
   * Constante: nom de la bdd
   *
   * @var string
   */
  const DEFAULT_SQL_DTB = 'rsslounge';
 
  /**
   * Constructeur
   *
   * @param void
   * @return void
   * @see PDO::__construct()
   * @access private
   */
  private function __construct() {
    $this->PDOInstance = 
      new PDO('mysql:dbname='.self::DEFAULT_SQL_DTB.';host='.self::DEFAULT_SQL_HOST,
      self::DEFAULT_SQL_USER,
      self::DEFAULT_SQL_PASS
    );
  }
 
  /**
   * Crée et retourne l'objet
   *
   * @access public
   * @static
   * @param void
   * @return $instance
   */
public static function getInstance() {
  if(is_null(self::$instance)) {
    self::$instance = new pdo55();
  }
  return self::$instance;
}
 
  /**
   * Exécute une requête SQL avec PDO
   *
   * @param string $query La requête SQL
   * @return PDOStatement Retourne l'objet PDOStatement
   */
  public function query($query) {
    return $this->PDOInstance->query($query);
  }
  
  /**
   * Exécute une requête SQL avec PDO avec la méthode prepare
   *
   * @param string $query La requête SQL
   * @return PDOStatement Retourne l'objet PDOStatement
   */
  public function prepare($query, $values) {
    $sth = $this->PDOInstance->prepare($query);
    $sth->execute($values);
    return $sth->fetchAll();
  }
}
