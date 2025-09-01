<?php

namespace Tests\Unit\Enums;

use App\Enums\Assessment\QuestionResponseTypes;
use PHPUnit\Framework\TestCase;

class QuestionResponseTypesTest extends TestCase
{
    /**
     * Test that the enum values are correctly defined.
     */
    public function test_enum_values()
    {
        $this->assertEquals('text', QuestionResponseTypes::text->value);
        $this->assertEquals('number', QuestionResponseTypes::number->value);
        $this->assertEquals('rating', QuestionResponseTypes::rating->value);
        $this->assertEquals('textarea', QuestionResponseTypes::textarea->value);
        $this->assertEquals('select', QuestionResponseTypes::select->value);
        $this->assertEquals('multiselect', QuestionResponseTypes::multiselect->value);
        $this->assertEquals('checkbox', QuestionResponseTypes::checkbox->value);
        $this->assertEquals('radio', QuestionResponseTypes::radio->value);
        $this->assertEquals('date', QuestionResponseTypes::date->value);
        $this->assertEquals('time', QuestionResponseTypes::time->value);
        $this->assertEquals('datetime', QuestionResponseTypes::datetime->value);
        $this->assertEquals('file', QuestionResponseTypes::file->value);
    }

    /**
     * Test that the label method returns the correct labels.
     */
    public function test_enum_labels()
    {
        $this->assertEquals('Text', QuestionResponseTypes::text->label());
        $this->assertEquals('Number', QuestionResponseTypes::number->label());
        $this->assertEquals('Rating', QuestionResponseTypes::rating->label());
        $this->assertEquals('Text Area', QuestionResponseTypes::textarea->label());
        $this->assertEquals('Select Dropdown', QuestionResponseTypes::select->label());
        $this->assertEquals('Multi-Select', QuestionResponseTypes::multiselect->label());
        $this->assertEquals('Checkbox', QuestionResponseTypes::checkbox->label());
        $this->assertEquals('Radio Button', QuestionResponseTypes::radio->label());
        $this->assertEquals('Date', QuestionResponseTypes::date->label());
        $this->assertEquals('Time', QuestionResponseTypes::time->label());
        $this->assertEquals('Date & Time', QuestionResponseTypes::datetime->label());
        $this->assertEquals('File Upload', QuestionResponseTypes::file->label());
    }

    /**
     * Test that we can create an enum from a string value.
     */
    public function test_from_string()
    {
        $this->assertEquals(QuestionResponseTypes::text, QuestionResponseTypes::from('text'));
        $this->assertEquals(QuestionResponseTypes::number, QuestionResponseTypes::from('number'));
        $this->assertEquals(QuestionResponseTypes::rating, QuestionResponseTypes::from('rating'));
    }

    /**
     * Test that trying to create an enum from an invalid string throws an exception.
     */
    public function test_invalid_value()
    {
        $this->expectException(\ValueError::class);
        QuestionResponseTypes::from('invalid_value');
    }
}
