<?php namespace Cartalyst\Sentry;

interface UserInterface
{
	public function findById($id);

	public function findByLogin($login);

	public function findByCredentials($login, $password);

	public function activate($login, $activationCode);

	public function resetPassword($login, $password);

	public function confirmResetPassword($login, $resetCode);

	public function clearResetPassword(UserInterface $user);
}