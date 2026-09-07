<script setup>
import { Head } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';
import PublicFooter from '@/Components/PublicFooter.vue';
</script>

<template>
    <Head title="Policy" />

    <div class="flex min-h-screen flex-col bg-paper text-ink">
        <PublicHeader />

        <main class="mx-auto w-full max-w-2xl flex-1 px-6 py-16">
            <h1 class="text-2xl font-semibold tracking-tight">Privacy Policy</h1>
            <p class="mt-2 text-sm text-ink/50">Last updated September 7, 2026.</p>

            <p class="mt-6 text-ink/70">
                getyourcv ("we") lets you build a resume, publish it at a link, and tailor it to a
                specific job. This page explains what data we collect, why, and how you can control
                or remove it. We are the data controller for the information described here; reach
                us any time at
                <a href="mailto:getyourcv.team@gmail.com" class="underline hover:opacity-60">getyourcv.team@gmail.com</a>.
            </p>

            <h2 class="mt-10 text-lg font-semibold">What we collect</h2>
            <p class="mt-2 text-ink/70">
                An account: name and email. Resume content: whatever you type into the builder -
                contact details, links, About, skills, experience, education, courses and
                certifications. If you use vacancy matching, the job description text you paste in.
                We only ask for what the resume itself needs.
            </p>

            <h2 class="mt-10 text-lg font-semibold">Why we process it</h2>
            <p class="mt-2 text-ink/70">
                To provide the service you asked for - storing your resume, rendering your public
                page and PDF, and running the features you click on. Creating an account means you
                agree to this policy.
            </p>

            <h2 class="mt-10 text-lg font-semibold">AI features</h2>
            <p class="mt-2 text-ink/70">
                The AI rewrite and vacancy-matching features send the relevant text (a bullet point,
                your About section, or the pasted vacancy text) to a third-party AI provider to
                generate suggestions. That text is processed only to answer your request and is not
                used by us to train any model.
            </p>

            <h2 class="mt-10 text-lg font-semibold">Other third parties</h2>
            <p class="mt-2 text-ink/70">
                We rely on a small number of subprocessors to run the service: our hosting and
                database provider, a transactional email provider (for sign-up and password-reset
                emails), and the AI provider mentioned above. We do not sell your data, and we do not
                share it for advertising.
            </p>

            <h2 class="mt-10 text-lg font-semibold">Public resume links</h2>
            <p class="mt-2 text-ink/70">
                Publishing a resume makes it viewable by anyone who has the link - that is the whole
                point of the product. Public resume pages are marked so search engines do not index
                them. You can hide, archive or delete a resume at any time, which immediately makes
                the link stop working.
            </p>

            <h2 class="mt-10 text-lg font-semibold">View analytics</h2>
            <p class="mt-2 text-ink/70">
                When someone opens your resume link, we log a one-way hash of the visitor rather than
                their raw IP address or any identifying detail, so you can see view counts without us
                storing who looked at it.
            </p>

            <h2 class="mt-10 text-lg font-semibold">How long we keep data</h2>
            <p class="mt-2 text-ink/70">
                A deleted resume goes to Trash first and stays there, fully recoverable, until you
                permanently delete it yourself. Version history is kept until you remove individual
                versions. Deleting your account erases your resumes, their versions and view history
                immediately and permanently.
            </p>

            <h2 class="mt-10 text-lg font-semibold">Your rights</h2>
            <p class="mt-2 text-ink/70">
                You can view and correct your data any time by editing it in the app. You can delete
                individual resumes (via Trash) or your entire account and all associated data (via
                Delete Account on your Profile page) yourself, without contacting us. For anything else -
                a copy of your data, or a question about how it's used - email us at
                <a href="mailto:getyourcv.team@gmail.com" class="underline hover:opacity-60">getyourcv.team@gmail.com</a>.
            </p>

            <h2 class="mt-10 text-lg font-semibold">Security</h2>
            <p class="mt-2 text-ink/70">
                Traffic to getyourcv is served over HTTPS, and passwords are stored hashed, never in
                plain text.
            </p>

            <h2 class="mt-10 text-lg font-semibold">Cookies</h2>
            <p class="mt-2 text-ink/70">
                We use a single session cookie to keep you signed in. We do not use advertising or
                tracking cookies.
            </p>

            <h2 class="mt-10 text-lg font-semibold">Changes to this policy</h2>
            <p class="mt-2 text-ink/70">
                If this policy changes materially, we'll update the date at the top of this page.
            </p>
        </main>

        <PublicFooter />
    </div>
</template>
