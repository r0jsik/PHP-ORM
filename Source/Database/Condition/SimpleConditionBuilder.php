<?php
namespace Source\Database\Condition;

class SimpleConditionBuilder implements ConditionBuilder
{
    /**
     * @var string A query's fragment that is being built.
     */
    private $condition;

    /**
     * @var array An associative array mapping object's property names to corresponding column names.
     */
    private $column_names;

    private $parameters;

    /**
     * @param array $column_names An associative array mapping object's property names to corresponding column names.
     */
    public function __construct(array $column_names)
    {
        $this->condition = "";
        $this->column_names = $column_names;
        $this->parameters = [];
    }

    /**
     * @inheritDoc
     */
    public function property(string $name): ConditionBuilder
    {
        $column_name = $this->column_names[$name];
        $this->append("`$column_name`");

        return $this;
    }

    /**
     * @param string $token A token that will be appended to the query.
     * @param string... $parameters A list of parameters which will be used to prepare the query.
     * @return ConditionBuilder
     */
    private function append(string $token, ...$parameters): ConditionBuilder
    {
        $this->condition .= $token;

        foreach ($parameters as $parameter)
        {
            $this->parameters[] = $parameter;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function and(): ConditionBuilder
    {
        return $this->append(" AND ");
    }

    /**
     * @inheritDoc
     */
    public function or(): ConditionBuilder
    {
        return $this->append(" OR ");
    }

    /**
     * @inheritDoc
     */
    public function not(): ConditionBuilder
    {
        return $this->append(" NOT ");
    }

    /**
     * @inheritDoc
     */
    public function like(string $pattern): ConditionBuilder
    {
        return $this->append(" LIKE ?", $pattern);
    }

    /**
     * @inheritDoc
     */
    public function in(string ...$options): ConditionBuilder
    {
        foreach ($options as $option)
        {
            $this->parameters[] = $option;
        }

        $placeholder =  str_repeat("?, ", sizeof($options) - 1) . "?";
        $this->append(" IN ($placeholder)", ...$options);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function between(int $from, int $to): ConditionBuilder
    {
        return $this->append(" BETWEEN ? TO ?", $from, $to);
    }

    /**
     * @inheritDoc
     */
    public function lt(int $value): ConditionBuilder
    {
        return $this->append(" < ?", $value);
    }

    /**
     * @inheritDoc
     */
    public function le(int $value): ConditionBuilder
    {
        return $this->append(" <= ?", $value);
    }

    /**
     * @inheritDoc
     */
    public function gt(int $value): ConditionBuilder
    {
        return $this->append(" > ?", $value);
    }

    /**
     * @inheritDoc
     */
    public function ge(int $value): ConditionBuilder
    {
        return $this->append(" >= ?", $value);
    }

    /**
     * @inheritDoc
     */
    public function eq(string $value): ConditionBuilder
    {
        return $this->append(" = ?", $value);
    }

    /**
     * @inheritDoc
     */
    public function ne(string $value): ConditionBuilder
    {
        return $this->append(" <> ?", $value);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->condition;
    }

    public function get_parameters(): array
    {
        return $this->parameters;
    }
}
