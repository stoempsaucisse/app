<?php

use Microffice\User;

class HelpersTest extends TestCase
{

    /**
     * Test removeValidationRule.
     *
     * @return void
     */
    public function testRemoveValidationRule()
    {
        $badRules = [
            'confirmed',
            'different:anotherfield',
            'required_if:anotherfield,value',
            'required_unless:anotherfield,value,value2',
            'required_with_all:anotherfield,value,value2,value3'
        ];

        foreach ($badRules as $badRule) {
            $rule = "alpha|beta:field,value|$badRule";
            $ret = removeValidationRule($rule, $badRule);

            $this->assertTrue(is_string($ret));
            $this->assertContains('alpha|', $ret);
            $this->assertContains('|beta:field,value', $ret);
            $this->assertNotContains('beta:field,value|', $ret);
            $this->assertNotContains($badRule, $ret);

            $rule = "alpha|$badRule|omega:field,value,value2";
            $ret = removeValidationRule($rule, $badRule);

            $this->assertTrue(is_string($ret));
            $this->assertContains('alpha|', $ret);
            $this->assertContains('|omega:field,value,value2', $ret);
            $this->assertNotContains($badRule, $ret);

            $rule = "$badRule|gamma:field,value|omega";
            $ret = removeValidationRule($rule, $badRule);

            $this->assertTrue(is_string($ret));
            $this->assertContains('gamma:field,value|', $ret);
            $this->assertContains('|omega', $ret);
            $this->assertNotContains('|gamma:field,value', $ret);
            $this->assertNotContains($badRule, $ret);
        }

        // Test with array of rules as input
        $badRule = 'unique:table,column';
        $rules = [
            'name' => "alpha:field|beta|$badRule",
            'lastname' => "alpha|$badRule|omega",
            'email' => "$badRule|alpha|omega"
            ];
        $ret = removeUniqueRules($rules);

        $this->assertTrue(is_array($ret));
        $this->assertContains('alpha:field|beta', $ret['name']);
        $this->assertNotContains('alpha:field|beta|', $ret['name']);
        $this->assertNotContains($badRule, $ret['name']);
        $this->assertContains('alpha|omega', $ret['lastname']);
        $this->assertNotContains($badRule, $ret['lastname']);
        $this->assertContains('alpha|omega', $ret['email']);
        $this->assertNotContains('|alpha|omega', $ret['email']);
        $this->assertNotContains($badRule, $ret['email']);

    }

    /**
     * Test getStandaloneValidationRules.
     *
     * @return void
     */
    public function testGetStandaloneValidationRules()
    {
        // As of today, these are the rules that depend on other fields :
        //
        // * after: (could use another field)
        // * before: (not sure, but no reason it does not work as after:)
        // * confirmed
        // * different:
        // * in_array:
        // * required_*:
        // * same:
        $badRules = [
            'confirmed',
            'different:anotherfield',
            'in_array:anotherfield',
            'required_if:anotherfield,value',
            'required_if:anotherfield,value,value2',
            'required_unless:anotherfield,value3',
            'required_with:field',
            'required_with:field2,field3',
            'required_with_all:field',
            'required_with_all:field2,field3',
            'required_without:field',
            'required_without:field2,field3',
            'required_without_all:field',
            'required_without_all:field2,field3',
            'same:anotherfield',
        ];

        // Test regex is OK :)
        foreach ($badRules as $badRule) {
        // Test with (string) $rules and without $fields
            // 1) Test when rules definition starts with a rule to strip
            $rules = $badRule . '|alpha|omega';
            $ret = getStandaloneValidationRules($rules);

            $this->assertTrue(is_string($ret));
            $this->assertContains('alpha|', $ret);
            $this->assertContains('|omega', $ret);
            $this->assertNotContains($badRule, $ret);

            // 2) Test when rules definition ends with a rule to strip
            $rules = 'alpha|omega|' . $badRule;
            $ret = getStandaloneValidationRules($rules);

            $this->assertTrue(is_string($ret));
            $this->assertContains('alpha|', $ret);
            $this->assertContains('|omega', $ret);
            $this->assertNotContains($badRule, $ret);

            // 3) Test when rules definition contains a rule to strip
            $rules = 'alpha|' . $badRule . '|omega';
            $ret = getStandaloneValidationRules($rules);

            $this->assertTrue(is_string($ret));
            $this->assertContains('alpha|', $ret);
            $this->assertContains('|omega', $ret);
            $this->assertNotContains($badRule, $ret);

            // 4) Test when rules definition contains two adjecent rules to strip
            $otherBadRule = $badRules[rand(0, count($badRules) - 1)];
            $rules = 'alpha|' . $badRule . '|' . $otherBadRule . '|omega';
            $ret = getStandaloneValidationRules($rules);

            $this->assertTrue(is_string($ret));
            $this->assertContains('alpha|', $ret);
            $this->assertContains('|omega', $ret);
            $this->assertNotContains($badRule, $ret);
            $this->assertNotContains($otherBadRule, $ret);
        }

        // 5) Test that required is not striped
        $rules = 'alpha|required|omega';
        $ret = getStandaloneValidationRules($rules);

        $this->assertTrue(is_string($ret));
        $this->assertContains($rules, $ret);

        // 6) Test that after: and before: are not striped when $fields is not supplied
        $badRule = 'after:anotherfield|beta|before:anotherfield|gamma';
        $rules = 'alpha|' . $badRule . '|omega';
        $ret = getStandaloneValidationRules($rules);

        $this->assertTrue(is_string($ret));
        $this->assertContains('alpha|', $ret);
        $this->assertContains('|omega', $ret);
        $this->assertContains($badRule, $ret);

        // 7) Test that after: and before: are striped when $fields is supplied
        $badRule = 'after:anotherfield|beta|before:tomorrow|gamma';
        $rules = 'alpha|' . $badRule . '|omega';
        $ret = getStandaloneValidationRules($rules, 'anotherfield,somefield');

        $this->assertTrue(is_string($ret));
        $this->assertContains('alpha|', $ret);
        $this->assertContains('|omega', $ret);
        $this->assertContains('before', $ret);
        $this->assertNotContains($badRule, $ret);


        // Tests with only (array) $rules
        $badRule = $badRules[rand(0, count($badRules) - 1)];
        $otherBadRule = $badRules[rand(0, count($badRules) - 1)];
        $rules = array(
            'name' => 'alpha|' . $badRule . '|omega',
            'email' => 'gamma|' . $otherBadRule);
        $ret = getStandaloneValidationRules($rules);

        $this->assertTrue(is_array($ret));
        $this->assertContains('alpha|', $ret['name']);
        $this->assertContains('|omega', $ret['name']);
        $this->assertNotContains($badRule, $ret['name']);
        $this->assertContains('gamma', $ret['email']);
        $this->assertNotContains($otherBadRule, $ret['email']);

        // Test (array) $rules and (string) $fields
        $badRule = $badRules[rand(0, count($badRules) - 1)];
        $otherBadRule = $badRules[rand(0, count($badRules) - 1)];
        $rules = array(
            'name' => 'alpha|' . $badRule . '|omega',
            'email' => 'gamma|' . $otherBadRule);
        $fields = 'email,name';
        $ret = getStandaloneValidationRules($rules, $fields);

        $this->assertTrue(is_array($ret));
        $this->assertContains('alpha|', $ret['name']);
        $this->assertContains('|omega', $ret['name']);
        $this->assertNotContains($badRule, $ret['name']);
        $this->assertContains('gamma', $ret['email']);
        $this->assertNotContains($otherBadRule, $ret['email']);

        // Test $rules and $fields are arrays
        $badRule = $badRules[rand(0, count($badRules) - 1)];
        $otherBadRule = $badRules[rand(0, count($badRules) - 1)];
        $rules = array(
            'name' => 'alpha|' . $badRule . '|omega',
            'email' => 'gamma|' . $otherBadRule);
        $fields = ['email', 'name'];
        $ret = getStandaloneValidationRules($rules, $fields);

        $this->assertTrue(is_array($ret));
        $this->assertContains('alpha|', $ret['name']);
        $this->assertContains('|omega', $ret['name']);
        $this->assertNotContains($badRule, $ret['name']);
        $this->assertContains('gamma', $ret['email']);
        $this->assertNotContains($otherBadRule, $ret['email']);
    }

    /**
     * Test removeUniqueRules.
     *
     * @return void
     */
    public function testRemoveUniqueRules()
    {
        $params = ['table', 'column'];
        $optionalParams = ['id', 'idColumn', ''];
        // Test for all 3 forms of unique: rule
        foreach ($optionalParams as $optionalParm) {
        // Test all positions of unique: in rule string
            $badRule = 'unique:' . implode(',', $params);
            $rules = "alpha|beta|$badRule";
            $ret = removeUniqueRules($rules);

            $this->assertTrue(is_string($ret));
            $this->assertContains('alpha|beta', $ret);
            $this->assertNotContains('alpha|beta|', $ret);
            $this->assertNotContains($badRule, $ret);

            $rules = "alpha|$badRule|omega";
            $ret = removeUniqueRules($rules);

            $this->assertTrue(is_string($ret));
            $this->assertContains('alpha|', $ret);
            $this->assertContains('|omega', $ret);
            $this->assertNotContains($badRule, $ret);

            $rules = "$badRule|beta|omega";
            $ret = removeUniqueRules($rules);

            $this->assertTrue(is_string($ret));
            $this->assertContains('beta|omega', $ret);
            $this->assertNotContains('|beta|omega', $ret);
            $this->assertNotContains($badRule, $ret);

            array_push($params, $optionalParm);
        }

        // Test with array of rules as input
        $badRule = 'unique:' . implode(',', $params);
        $rules = [
            'name' => "alpha|beta|$badRule",
            'lastname' => "alpha|$badRule|omega",
            'email' => "$badRule|alpha|omega"
            ];
        $ret = removeUniqueRules($rules);

        $this->assertTrue(is_array($ret));
        $this->assertContains('alpha|beta', $ret['name']);
        $this->assertNotContains('alpha|beta|', $ret['name']);
        $this->assertNotContains($badRule, $ret['name']);
        $this->assertContains('alpha|omega', $ret['lastname']);
        $this->assertNotContains($badRule, $ret['lastname']);
        $this->assertContains('alpha|omega', $ret['email']);
        $this->assertNotContains('|alpha|omega', $ret['email']);
        $this->assertNotContains($badRule, $ret['email']);
    }
}
