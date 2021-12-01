<?php 

namespace App\controllers;
if( !session_id() ) @session_start();

use App\QueryBuilder;
use Delight\Auth\Auth;
use League\Plates\Engine;
use \Delight\Auth\Role;
use \Tamtamchik\SimpleFlash\Flash;



/**
 * 
 */
class User
{
	private $auth;
	private $db;

	function __construct(QueryBuilder $db , Auth $auth, Engine $templates)
	{
		$this->db = $db;
		$this->auth = $auth;
		$this->templates = $templates;
	}

	public function login()
	{

		try {
		    $this->auth->login($_POST['email'], $_POST['password']);

		}
		catch (\Delight\Auth\InvalidEmailException $e) {
			flash()->error('Неверный email!');
		}
		catch (\Delight\Auth\InvalidPasswordException $e) {
		    flash()->error('Неверный пароль!');
		}

		if (!$this->auth->isLoggedIn()) {
			header('Location: /page_login'); exit;
		}

		if ($this->auth->getRoles() == [1 => 'ADMIN']) {
			flash()->success('Выполнен вход как Администратор');
			header('Location: /users'); exit;
		}

		flash()->success('Выполнен вход как ' . $this->auth->getUsername());

		header('Location: /users'); exit;
		
		
	}

	public function edit_user_info($id)
	{

		$this->db->update([
					'username' => $_POST['username'],
					'job' => $_POST['job'],
					'phone' => $_POST['phone'],
					'adress' => $_POST['adress']
				], $id);

		flash()->success('Профиль успешно обновлен!');

		header('Location: /users'); exit;

	}

	public function set_media($id)
	{

		move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);

		$this->db->update([
					'media' => addslashes($_FILES['file']['name'])
				], $id);

		flash()->success('Аватарка загружена!');

		header('Location: /users'); exit;

	}

	public function set_status($id)
	{

		$this->db->update([
					'online_status' => $_POST['online_status']
				], $id);

		flash()->success('Статус установлен!');
		
		header('Location: /users'); exit;
	}

	public function delete_user($id)
	{
		$this->db->delete($id);

		flash()->success('Профиль удален!');

		header('Location: /users'); exit;
	}

	public function edit_security($id)
	{
		
		$data['password'] = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
		$data['email'] = $_POST['email']; 

		$this->db->update($data, $id);

		flash()->success('Данные авторизации успешно изменены!');
		
		header('Location: /users'); exit;
	}

	public function logout()
	{
		$this->auth->logOut();
		header('Location: /page_login'); exit;
	}
			

}

