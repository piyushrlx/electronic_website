# TECHFUSION — SEQUENTIAL EXECUTION AND CHECKPOINT CONTROL

## 1. Current Project State

Project Name: TechFusion  
Project Directory: `D:\XAMP\htdocs\electronic_website`  
Master Instruction File: `D:\XAMP\htdocs\electronic_website\techfusion_antigravity_prompt.md`  
GitHub Repository: `https://github.com/piyushrlx/electronic_website`  
Primary Branch: `main`

The execution report provided in the current task describes previous implementation work, test results, and GitHub commits. Treat this report as an initial reference, not as independently verified evidence of the current repository state.

Before making further changes, inspect the actual project, Git history, working tree, existing documentation, test results, and current phase tracker.

Determine which phases are genuinely complete, which are partially complete, and which require additional verification.

Do not recreate completed functionality unnecessarily.

## 2. Mandatory One-Phase-at-a-Time Rule

**Execute exactly one phase at a time. Do not attempt to implement, complete, or advance multiple phases in a single execution cycle.**

For every phase:

1. Inspect the current project state and relevant dependencies.
2. Confirm the phase objective, scope, prerequisites, and acceptance criteria.
3. Prepare a focused implementation plan for that phase only.
4. Identify expected file changes, security implications, risks, and dependencies.
5. Execute only the approved work within the current phase.
6. Validate the changes using appropriate checks and tests.
7. Review the results against the phase's acceptance criteria.
8. Update the master phase tracker and supporting documentation.
9. Produce the mandatory phase completion report.
10. Stop at the checkpoint and wait for explicit user approval before proceeding to the next phase.

Do not automatically begin the next phase after finishing the current one.

Do not interpret permission to work on TechFusion as permission to execute the entire project without checkpoints.

## 3. Mandatory Checkpoint Protocol

Every phase must end with a checkpoint containing the following information:

- **Phase status:** Current status using the approved tracking values.
- **Work completed:** Specific changes actually made.
- **Files affected:** Files created, modified, or removed.
- **Validation evidence:** Commands executed, tests performed, and actual results.
- **Security review:** Relevant findings, risks, and remediation status.
- **Outstanding issues:** Defects, blockers, unresolved decisions, and incomplete requirements.
- **Acceptance criteria:** Each criterion marked as passed, failed, or not yet verified.
- **Git status:** Current branch, working-tree status, and commit details where applicable.
- **Documentation status:** Tracker and relevant reports updated or outstanding.
- **Next phase:** Proposed next phase and its prerequisites.

The checkpoint must clearly distinguish completed work from work that is merely planned or partially verified.

If a phase fails validation, mark it `VALIDATION FAILED` or `BLOCKED`, explain the reason, and remain within that phase until the issue is addressed or an explicit decision is made.

## 4. Explicit Approval Gate

After presenting each checkpoint, stop execution.

Wait for an explicit instruction from the user before beginning another phase.

The following instructions must not be treated as automatic authorization to skip checkpoints:

- General requests to continue the project.
- Statements that the overall project should be completed.
- A previous approval for a different phase.
- An agent's own conclusion that the next phase is straightforward.

Approval to proceed applies to the next identified phase only, unless the user explicitly establishes a different scope.

If approval is unclear, request clarification rather than advancing automatically.

## 5. GitHub and Change-Control Requirements

Preserve the existing repository history and unrelated user changes.

Before making changes, inspect the working tree and identify uncommitted work. Never discard, overwrite, or reset unrelated changes without authorization.

Keep changes limited to the current phase. Review the final diff before staging or committing files.

Do not commit or push automatically at every checkpoint. Follow the established repository workflow and obtain authorization when an action could publish unfinished work, expose sensitive information, or alter shared project state.

Before any push, verify that the intended changes are the only changes being published and that sensitive credentials or environment files are not included.

Record actual commit hashes and push outcomes. Do not invent or assume successful Git operations.

## 6. Testing and Verification Requirements

Run checks relevant to the current phase, using the project's actual environment and available tooling.

Report:

- Exact commands executed.
- Actual test counts and outcomes.
- Failures, warnings, and skipped checks.
- Any environmental limitations.
- Whether tests modified application data or database state.
- Whether cleanup was performed and verified.

Do not rely exclusively on a previously generated test report when the current implementation has changed.

Do not claim that the site works correctly in a browser, that deployment succeeds, or that a security issue is resolved unless the corresponding behavior has been verified.

## 7. Phase Tracker Requirements

Maintain a single authoritative project tracker, such as `docs/TECHFUSION_PHASE_TRACKER.md`, subject to the existing repository structure and documentation conventions.

Track each phase's status, prerequisites, planned work, actual changes, acceptance criteria, validation evidence, risks, deliverables, and approval state.

Use these status values consistently:

- `NOT STARTED`
- `PLANNING`
- `IN PROGRESS`
- `BLOCKED`
- `READY FOR VALIDATION`
- `VALIDATION FAILED`
- `COMPLETED`

A phase may be marked `COMPLETED` only after its required work and acceptance criteria have been reviewed against actual evidence.

Where approval is required to advance, record that the phase is awaiting approval separately from its technical completion status.

## 8. Immediate Task — Reassessment Before Further Implementation

Begin by reassessing the current TechFusion repository and the supplied execution report.

Do not immediately start adding features, changing architecture, redesigning pages, modifying the database, or altering deployment configuration.

Determine the verified state of the project, identify any inconsistencies between the execution report and the repository, and establish the current phase and outstanding work.

Update the phase tracker and produce a concise reassessment report.

**STOP after this reassessment checkpoint. Do not implement the next phase until the user reviews the findings and explicitly approves the next step.**

## 9. Operating Principle

Work methodically, make narrowly scoped changes, verify the results, document the evidence, and stop at every required checkpoint.

The priority is not to complete the largest number of phases in the shortest time. The priority is to establish a reliable, secure, maintainable, and verifiable implementation of TechFusion without introducing regressions or losing control of the development process.