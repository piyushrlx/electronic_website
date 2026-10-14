# Project Work Style & Execution Rules (TechFusion)

This file defines the mandatory working style and behavioral rules for all AI assistants, agents, and contributors working in `D:\XAMP\htdocs\electronic_website`. These rules are derived directly from [execution.md](file:///d:/XAMP/htdocs/electronic_website/execution.md).

---

## 1. Mandatory One-Phase-at-a-Time Rule
- Execute exactly **one phase at a time**.
- Never attempt to implement, complete, or advance multiple phases in a single execution cycle.
- Do not automatically begin the next phase after completing the current one.
- Do not interpret general permission to work on TechFusion as permission to execute the entire project without checkpoints.

---

## 2. Mandatory Checkpoint Protocol
Every phase must conclude with a formal checkpoint containing:
1. **Phase Status:** Using only the approved tracking values (`NOT STARTED`, `PLANNING`, `IN PROGRESS`, `BLOCKED`, `READY FOR VALIDATION`, `VALIDATION FAILED`, `COMPLETED`).
2. **Work Completed:** Concrete changes actually executed.
3. **Files Affected:** Complete list of created, modified, or removed files.
4. **Validation Evidence:** Exact CLI commands executed, test suite output, counts, and real results.
5. **Security Review:** Security implications, defenses, and remediation status.
6. **Outstanding Issues:** Defects, blockers, unresolved items.
7. **Acceptance Criteria:** Marked as passed, failed, or unverified.
8. **Git Status:** Working-tree status, branch, and commit hash.
9. **Documentation Status:** Confirmation that [docs/TECHFUSION_PHASE_TRACKER.md](file:///d:/XAMP/htdocs/electronic_website/docs/TECHFUSION_PHASE_TRACKER.md) is updated.
10. **Next Phase:** Proposed next phase and its prerequisites.

---

## 3. Explicit Approval Gate
- **After presenting each checkpoint, stop execution immediately.**
- Wait for an explicit, unambiguous instruction from the user before starting the next phase.
- The following do NOT count as approval to skip checkpoints:
  - General requests to "continue" or "make it complete".
  - A previous approval granted for a different phase.
  - The assistant's own assumption that the next phase is small or straightforward.

---

## 4. Change Control & Git Hygiene
- Preserve existing repository history and data integrity.
- Never commit or expose `.env` or sensitive credentials.
- Test and verify diffs before staging.
- Record actual commit hashes and push outcomes.

---

## 5. Testing & Verification Standards
- Always run live tests using the actual PHP/MariaDB environment (`php tests/run_tests.php`).
- Report real test counts and execution outcomes.
- Never claim tests pass or that a feature works without verifiable evidence.

---

## 6. Single Authoritative Tracker
- Maintain [docs/TECHFUSION_PHASE_TRACKER.md](file:///d:/XAMP/htdocs/electronic_website/docs/TECHFUSION_PHASE_TRACKER.md) as the sole source of truth for project phase progress.
