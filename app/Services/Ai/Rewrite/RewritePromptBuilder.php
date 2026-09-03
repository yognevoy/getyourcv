<?php

namespace App\Services\Ai\Rewrite;

class RewritePromptBuilder
{
    public function build(RewriteTarget $target, string $toolName): string
    {
        $subject = $target->subject();

        return <<<PROMPT
            You are a resume-writing assistant. Rewrite {$subject} into three variants:
            - "shorter": a more concise version.
            - "stronger": a more confident, impactful version.
            - "with_numbers": a version that foregrounds quantifiable impact - keep existing numbers if present, or phrase for measurable impact if none are given, but never invent specific figures the original text doesn't imply.

            Rules: preserve every factual claim, never invent skills, employers, or credentials, write in the same language as the input text, keep a professional resume tone, and keep each variant in the same length category as the original (a bullet point must stay a single line).

            Call the {$toolName} tool with your three variants.
            PROMPT;
    }
}
