<?php 

namespace App\controllers;
if( !session_id() ) @session_start();


use PDO;
use League\Plates\Engine;
use App\QueryBuilder;
use Delight\Auth\Auth;
use \Delight\Auth\Role;




class HomeController
{
	private $db;
	private $templates;
	
	function __construct( QueryBuilder $db, Engine $templates, Auth $auth)
	{
		$this->db = $db;
		$this->templates = $templates;
		$this->auth = $auth;

	}

	public function get_users()
	{
		if (!$this->auth->isLoggedIn()) {
			flash()->error('Требуется авторизация!');            
		}
		if (!$this->auth->hasRole(Role::ADMIN)) {
			$id = $this->auth->getUserId();
			$post = $this->db->selectOne($id);
            echo $this->templates->render('users',['postsAsUser' => $post]);
            exit;
           }
		$posts = $this->db->selectAll(); 
		
		echo $this->templates->render('users', ['postsAsAdmin' => $posts]); exit;
	}

	public function page_register()
	{

		echo $this->templates->render('page_register'); exit;
	}

	public function create_user()
	{

		echo $this->templates->render('create_user'); exit;
	}

	public function edit_profile($id)
	{

		$profile = $this->db->selectOne($id);
		echo $this->templates->render('edit', ['profile' => $profile]); exit;
	}

	public function media($id)
	{

		echo $this->templates->render('media', ['id' => $id]); exit;
	}

	public function page_login()
	{

		echo $this->templates->render('page_login'); exit;
	}

	public function page_profile($id)
	{
		$profile = $this->db->selectOne($id);
		echo $this->templates->render('page_profile', ['profile' => $profile]); exit;
	}

	public function page_security($id)
	{
		$profile = $this->db->selectOne($id);
		echo $this->templates->render('security', ['id' => $id,
													'profile' => $profile]); 
		exit;
	}

	public function status($id)
	{
		$user = $this->db->selectOne($id);
		echo $this->templates->render('status', ['id' => $id,
												'status' => $user['online_status']]);
		exit;
	}
}