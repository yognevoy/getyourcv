<?php

namespace App\Services\Ai\Match;

class MatchPromptBuilder
{
    public function build(string $resumeSummary, string $toolName): string
    {
        return <<<PROMPT
            You are a resume-to-vacancy matching assistant. Here is the candidate's resume summary:

            {$resumeSummary}

            Compare it against the vacancy the user provides next. Identify the key skills and requirements the vacancy asks for, then classify each as either present in the resume ("matched_skills") or missing from it ("missing_skills"). Give an overall match score from 0 (no fit) to 100 (excellent fit) based on skills and experience overlap, and a one to two sentence summary of the fit.

            Rules: base your assessment only on what the resume and vacancy actually say, never invent skills or claims for either side, keep skill names short (as they'd appear as tags), and write the summary in the same language as the vacancy.

            Call the {$toolName} tool with your assessment.
            PROMPT;
    }
}
