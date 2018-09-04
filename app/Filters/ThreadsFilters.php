<?php 

namespace App\Filters;

use App\User;
use Illuminate\Http\Request;

class ThreadsFilters extends Filters
{
	protected $filters = ['by','popularity','unanswered'];

	public function unanswered()
	{
		return $this->builder->where('replies_count',0);
	}

	protected function by($username)
	{
		$user = User::where('name',$username)->firstOrFail();

		return $this->builder->where('user_id',$user->id);
	}

	/**
	 * @return mixed
	 */
	public function popularity()
	{
		$this->builder->getQuery()->orders = [];

		return $this->builder->orderBy('replies_count','desc');
	}
}