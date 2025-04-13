<?php

declare(strict_types=1);

namespace Php\Rules;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\NullableType;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\IdentifierRuleError;

/**
 * Custom PHPStan rule that checks for short nullable type syntax, enforcing the use of `|null` instead of `?`.
 *
 * @implements Rule<FunctionLike>
 */
final class NoShortNullableTypeRule implements Rule
{
    public function getNodeType(): string
    {
        return FunctionLike::class;
    }

    /**
     * @param FunctionLike $node
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];

        foreach ($node->getParams() as $param) {
            if ($param->type instanceof NullableType) {
                $errors[] = RuleErrorBuilder::message(sprintf(
                    'Do not use shorthand "?%s". Use "%s|null" instead.',
                    $param->type->type,
                    $param->type->type
                ))->line($param->getLine())->identifier('noShortNullableType')->build();
            }
        }

        $returnType = $node->getReturnType();
        if ($returnType instanceof NullableType) {
            $type = $returnType->type;
            $errors[] = RuleErrorBuilder::message(sprintf(
                'Do not use shorthand "?%s". Use "%s|null" instead in return type.',
                $type,
                $type
            ))->line($returnType->getLine())->identifier('noShortNullableType')->build();
        }

        return $errors;
    }
}
