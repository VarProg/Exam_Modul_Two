<?php 

namespace App;

use Aura\SqlQuery\QueryFactory;
use PDO;

/**
 * 
 */
class QueryBuilder
{
	private $pdo;
	private $queryFactory;
	
	public function __construct(PDO $pdo, QueryFactory $queryFactory)
	{
		$this->pdo = $pdo;
		$this->queryFactory = $queryFactory;
	}

	public function selectAll()
	{

		$select = $this->queryFactory->newSelect();

		$select->cols(['*'])
		    ->from('users');          // table name

		$sth = $this->pdo->prepare($select->getStatement());

		$sth->execute($select->getBindValues());

		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	public function selectOne($id)
	{

		$select = $this->queryFactory->newSelect();

		$select->cols(['*'])
		    ->from('users')          // table name
		    ->where("id = :id")
		    ->bindValue("id", $id);

		$sth = $this->pdo->prepare($select->getStatement());

		$sth->execute($select->getBindValues());

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		return $result;
	}

	public function getPassword($id)
	{

		$select = $this->queryFactory->newSelect();

		$select->cols([
					'password'
					])
		    ->from('users')          // table name
		    ->where("id = :id")
		    ->bindValue("id", $id);

		$sth = $this->pdo->prepare($select->getStatement());

		$sth->execute($select->getBindValues());

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		return $result;
	}

	public function insert($data)
	{
		$insert = $this->queryFactory->newInsert();

		$insert->into('users')             // insert into this table
		    ->cols($data);

		$sth = $this->pdo->prepare($insert->getStatement());

		$sth->execute($insert->getBindValues());

		$name = $insert->getLastInsertIdName('id');
		$id = $this->pdo->lastInsertId($name);
	}

	public function update($data, $id)
	{
		$update = $this->queryFactory->newUpdate();

		$update->table('users')           // update this table
		    ->cols($data)
		    ->where("id = :id", ['id' => $id])
		    ->bindValues(["id" => $id]);

		$sth = $this->pdo->prepare($update->getStatement());

		$sth->execute($update->getBindValues());
	}

	public function delete($id){
		$delete = $this->queryFactory->newDelete();

		$delete
		    ->from('users')                   // FROM this table
		    ->where('id = :id', ['id' => $id])	            // AND WHERE these conditions
		    ->bindValues(['id' => $id]);

		$sth = $this->pdo->prepare($delete->getStatement());

		$sth->execute($delete->getBindValues());

	}
}
















