<?php

use Microffice\User;

class HelpersTest extends TestCase
{
    /**
     * Test getStandaloneValidationRules.
     *
     * @return void
     */
    public function testGetStandaloneValidationRules()
    {
        // Test with only (string) $rules
        $different = 'different:name';
        $same = 'same:lastname';
        $required = 'required_if_not:password,50';
        $rules = 'alpha|confirmed|' . $different . '|beta|' . $same . '|' . $required . '|gamma';
        $ret = getStandaloneValidationRules($rules);

        $this->assertTrue(is_string($ret));
        $this->assertContains('alpha|', $ret);
        $this->assertContains('|beta|', $ret);
        $this->assertContains('|gamma', $ret);
        $this->assertNotContains('confirmed', $ret);
        $this->assertNotContains($different, $ret);
        $this->assertNotContains($same, $ret);
        $this->assertNotContains($required, $ret);

        // Test with only (array) $rules
        $rules = array(
            'name' => 'alpha|' . $required . '|max:255|unique:users|confirmed',
            'email' => 'digit|' . $same . '|' . $different . '|unique:users');
        $ret = getStandaloneValidationRules($rules);

        $this->assertTrue(is_array($ret));
        $this->assertContains('alpha|', $ret['name']);
        $this->assertContains('|max:255|unique:users', $ret['name']);
        $this->assertNotContains('confirmed', $ret['name']);
        $this->assertNotContains($required, $ret['name']);
        $this->assertContains('digit|', $ret['email']);
        $this->assertContains('|unique:users', $ret['email']);
        $this->assertNotContains($different, $ret['email']);
        $this->assertNotContains($same, $ret['email']);

        // Test both $rules and $fields are strings
        $rules = 'required|digit|' . $same . '|' . $different . '|unique:users';
        $fields = 'password';
        $ret = getStandaloneValidationRules($rules, $fields);

        $this->assertTrue(is_string($ret));
        $this->assertContains('required|digit|', $ret);
        $this->assertContains('|unique:users', $ret);
        $this->assertNotContains($same, $ret);
        $this->assertNotContains($different, $ret);

        // Test (array) $rules and (string) $fields
        $rules = array(
            'name' => 'alpha|' . $required . '|max:255|unique:users|confirmed',
            'email' => 'digit|' . $same . '|' . $different . '|unique:users',
            'password' => 'required|confirmed|min:6',
        );
        $fields = 'password,name';
        $ret = getStandaloneValidationRules($rules, $fields);

        $this->assertTrue(is_array($ret));
        $this->assertContains('alpha|', $ret['name']);
        $this->assertContains('|max:255|unique:users', $ret['name']);
        $this->assertNotContains('confirmed', $ret['name']);
        $this->assertNotContains($required, $ret['name']);
        $this->assertContains('required|', $ret['password']);
        $this->assertContains('|min:6', $ret['password']);
        $this->assertNotContains('confirmed', $ret['password']);

        // Test $rules and $fields are arrays
        $rules = array(
            'name' => 'alpha|' . $required . '|max:255|unique:users|confirmed',
            'email' => 'digit|' . $same . '|' . $different . '|unique:users',
            'password' => 'required|confirmed|min:6',
        );
        $fields = array('password', 'name');
        $ret = getStandaloneValidationRules($rules, $fields);

        $this->assertTrue(is_array($ret));
        $this->assertContains('alpha|', $ret['name']);
        $this->assertContains('|max:255|unique:users', $ret['name']);
        $this->assertNotContains('confirmed', $ret['name']);
        $this->assertNotContains($required, $ret['name']);
        $this->assertContains('required|', $ret['password']);
        $this->assertContains('|min:6', $ret['password']);
        $this->assertNotContains('confirmed', $ret['password']);
    }
    /**
     * Test addExceptionsToUniqueRules.
     *
     * @return void
     */
    public function testAddExceptionsToUniqueRules()
    {
        $rules = 'alpha|unique:users,name|gamma';
        $exceptions = '50,user_id';
        $ret = addExceptionsToUniqueRules($rules, $exceptions);

        $rules = User::$rules;
        $exceptions = '50,user_id';
        $ret = addExceptionsToUniqueRules($rules, $exceptions);
        dd($ret);

        $rules = array('email' => 'alpha|unique:users,name|gamma');
        $exceptions = array('password' => '50,user_id');
        $raised = 0;
        try
        {
            addExceptionsToUniqueRules($rules, $exceptions);
        }
        catch(Exception $expected)
        {
            $raised = 1;
        }
        if(!$raised)
        {
            $this->fail('Exception was not raised');
        }

        // Test should raise Exception when no column is provided
        $rules = 'alpha|unique:users|gamma';
        $exceptions = '50,user_id';
        $raised = 0;
        try
        {
            addExceptionsToUniqueRules($rules, $exceptions);
        }
        catch(Exception $expected)
        {
            $raised = 1;
        }
        if(!$raised)
        {
            $this->fail('Exception was not raised');
        }

    }
}
