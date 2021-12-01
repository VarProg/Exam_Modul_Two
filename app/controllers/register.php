<?php 

namespace App\controllers;

use App\QueryBuilder;
use Delight\Auth\Auth;
use League\Plates\Engine;
use \Tamtamchik\SimpleFlash\Flash;
use Faker\Factory;


/**
 * 
 */
class Register
{
	private $auth;
	private $db;

	function __construct(QueryBuilder $db , Auth $auth, Engine $templates)
	{
		$this->db = $db;
		$this->auth = $auth;
		$this->templates = $templates;

	}

	public function register()
	{
		try {
		    $userId = $this->auth->register($_POST['email'], $_POST['password'], '');
		}
		catch (\Delight\Auth\InvalidEmailException $e) {
		    flash()->error('<strong>Уведомление!</strong> Этот эл. адрес уже занят другим пользователем.');
		}
		catch (\Delight\Auth\InvalidPasswordException $e) {
		    die('Invalid password');
		}
		catch (\Delight\Auth\UserAlreadyExistsException $e) {
		    die('User already exists');
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
		    die('Too many requests');
		}

		flash()->success('Регистрация успешна!');

		header('Location: /page_login'); exit;

	}

	public function createUserAsAdmin()
	{

		try {
		    $userId = $this->auth->admin()->createUser($_POST['email'], $_POST['password'], $_POST['username']);

		}
		catch (\Delight\Auth\InvalidEmailException $e) {
		    die('Invalid email address');
		}
		catch (\Delight\Auth\InvalidPasswordException $e) {
		    die('Invalid password');
		}
		catch (\Delight\Auth\UserAlreadyExistsException $e) {
		    die('User already exists');
		}

		$this->db->update([
						'job' => $_POST['job'],
						'phone' => $_POST['phone'],
						'adress' => $_POST['adress'],
						'online_status' => $_POST['online_status'],
						'media' => $_POST['file'],
						'vk' => $_POST['vk'],
						'telegram' => $_POST['telegram'],
						'instagram' => $_POST['instagram']
					], $userId); 

		flash()->success('Новый пользователь добавлен!');

		header('Location: /users'); exit;

	}

	public function createFakerUserAsAdmin()
	{
		$faker = Factory::create();

		for ($i = 0; $i < 10; $i++) {

			$userId = $this->auth->admin()->createUser($faker->email(), $faker->password(), $faker->username());

			$this->db->update([
							'job' => $faker->company(),
							'phone' => $faker->phoneNumber(),
							'adress' => $faker->address(),
							'online_status' => 'online',
							'media' => 'avatar-m.png',
							'vk' => 'vk',
							'telegram' => 'telegram',
							'instagram' => 'instagram'
						], $userId); 

			flash()->success('Новый пользователь добавлен!');
		}

		header('Location: /users'); exit;

	}

	

}

