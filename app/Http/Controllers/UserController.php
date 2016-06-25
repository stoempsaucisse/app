<?php

namespace Microffice\Http\Controllers;

use Microffice\User;
use Microffice\Repositories\UserRepository;
use Microffice\Http\Requests;

use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Gate;
use Log;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * The user repository instance.
     */
    protected $users;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Show all users.
     *
     * @return Response
     */
    public function index()
    {
        $users = $this->users->findAll();
        return view('user.list', ['users' => $users]);
    }

    /**
     * Show form to create new user.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        if(Gate::denies('create', User::class))
        {
            throw new AuthorizationException(trans('error.create', ['resource' => trans_choice('user.user', 1)]), 403);
        }
        return view('user.form', ['action' => 'create']);
    }

    /**
     * Store new user in DB.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        if($this->users->saveNew($request->input('user')))
        {
            return redirect()->action('UserController@index');
        }
        return redirect()->action('UserController@create')->withInput($request->except(['password', 'password_confirmation']));
    }

    /**
     * Show user.
     *
     * @return Response
     */
    public function show($userId)
    {
        $user = $this->users->findById($userId);
        return view('user.show', ['action' => 'show', 'user' => $user]);
    }

    /**
     * Show edit form to update user.
     *
     * @return Response
     */
    public function edit($userId)
    {
        $user = $this->users->findById($userId);
        if(Gate::denies('update', [User::class, $userId]))
        {
            return redirect()->action('UserController@show', ['id' => $userId]);
            // throw new AuthorizationException(trans('error.update', ['resource' => trans_choice('user.user', 1)]), 403);
        }
        return view('user.form', ['action' => 'edit', 'user' => $user]);
    }

    /**
     * Update user to DB.
     *
     * @return Response
     */
    public function update(Request $request, $userId)
    {
        if($this->users->update($userId, array_filter($request->input('user'))))
        {
            return redirect()->action('UserController@index');
        }
        return redirect('user/' . $userId)->withInput($request->except(['password', 'password_confirmation']));
    }

    /**
     * remove user from DB.
     *
     * @return bool
     */
    public function destroy($userId)
    {
        if($this->users->delete($userId))
        {
            return redirect()->action('UserController@index');
        }
        return redirect('user/' . $userId);
    }
}
