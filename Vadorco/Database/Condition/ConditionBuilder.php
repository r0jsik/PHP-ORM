<?php
namespace Vadorco\Database\Condition;

interface ConditionBuilder
{
    /**
     * @param string $name The name of the property whose corresponding column name will be appended to the condition.
     * @return ConditionBuilder
     */
    public function property(string $name): ConditionBuilder;

    /**
     * @return ConditionBuilder
     */
    public function and(): ConditionBuilder;

    /**
     * @return ConditionBuilder
     */
    public function or(): ConditionBuilder;

    /**
     * @return ConditionBuilder
     */
    public function not(): ConditionBuilder;

    /**
     * @param string $pattern Preceding token has to be like this pattern.
     * @return ConditionBuilder
     */
    public function like(string $pattern): ConditionBuilder;

    /**
     * @param string... $options A list of available options that the preceding token can be equal to.
     * @return ConditionBuilder
     */
    public function in(string ...$options): ConditionBuilder;

    /**
     * @param int $from The lower limit of the number which the preceding token can be.
     * @param int $to The upper limit of the number which the preceding token can be.
     * @return ConditionBuilder
     */
    public function between(int $from, int $to): ConditionBuilder;

    /**
     * @param int $value Preceding token has to be less from this value.
     * @return ConditionBuilder
     */
    public function lt(int $value): ConditionBuilder;

    /**
     * @param int $value Preceding token has to be less or equal to this value.
     * @return ConditionBuilder
     */
    public function le(int $value): ConditionBuilder;

    /**
     * @param int $value Preceding token has to be greater from this value.
     * @return ConditionBuilder
     */
    public function gt(int $value): ConditionBuilder;

    /**
     * @param int $value Preceding token has to be greater or equal to this value.
     * @return ConditionBuilder
     */
    public function ge(int $value): ConditionBuilder;

    /**
     * @param string $value Preceding token has to be equal to this value.
     * @return ConditionBuilder
     */
    public function eq(string $value): ConditionBuilder;

    /**
     * @param string $value Preceding token has to be not equal to this value.
     * @return ConditionBuilder
     */
    public function ne(string $value): ConditionBuilder;

    /**
     * @return array An array of parameters that will be used to prepare the query.
     */
    public function get_parameters(): array;
}
