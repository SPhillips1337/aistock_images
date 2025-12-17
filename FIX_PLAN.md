FIX PLAN — Prompt Quality, Generation Stability, and Next Steps

Date: 2025-12-17
Owner: automation agent (you can trigger these steps)

Goal
- Improve image quality and reduce hallucinations (blurry images, malformed anatomy, unrelated content) while preserving creative enhancements from the enhanced prompt generator.

Summary of recent change
- We added an enhanced prompt engine (camera qualities, lighting, materials) and an optional negative instruction clause.
- We restored the generator helpers and indexer behavior so automation runs end-to-end again.
- We implemented a small A/B test and validated that adding negative constraints helped for the "Portrait" category.

Priority tasks (tomorrow)
1) Make negatives default in production
   - Status: Implemented (prompts now support include_negatives). Confirm the production generator calls generate_enhanced_prompt(..., include_negatives=True).
   - Goal: Reduce malformed anatomy and gibberish text across the board.

2) Run multi-category A/B sweep (8–12 samples each)
   - Categories: Portrait (people), Fitness/Action, Nature/Landscape, Product/Still Life, Architecture
   - For each category: generate N baseline and N negatives prompts, run vision QA, compute pass rates, save images + prompts to `ab_test_results.json`.
   - Metrics: QA pass rate, common QA reasons (anatomy, blur, unrelated), sample image check.

3) Prompt logging & telemetry
   - Add prompt → filename → QA result logging: write to `prompt_logs/` as JSON lines (timestamped) for each generated image.
   - This will make it easy to grep and analyze which prompt fragments correlate with failures.

4) Per-category prompt tuning
   - After review of A/B results, refine prompt templates per category: e.g., portraits favor studio lighting, action favors sharper shutter description, landscapes favor increased detail and HDR language.
   - Maintain a small map of category overrides inside enhanced_prompt_generator.py.

5) ComfyUI workflow tuning (sampler / steps)
   - Test increasing sampling steps + using a different sampler (e.g., Euler a -> DPM++/PLMS) on small sample sets to measure quality uplift vs throughput.
   - If quality improves, update workflow JSON used by the generator; run A/B to confirm improvements.

6) Canary & rollout strategy
   - Deploy negatives prompt to 20% of generated images for 24 hours (random sample) while logging prompts + QA outcomes. If pass rate improves, raise rollout to 100%.

7) Monitoring & alerts
   - Forward `automation_failures.log` to a simple alert webhook or email (cron wrapper is already logging failures). Add a short script that checks today's run count and failure count and emails if non-zero failures.

8) Housekeeping and cleanup
   - Remove any temporary override CSS files if not needed.
   - Consolidate duplicate `.bg-gradient` styles if present.
   - Remove test artifacts (ab_*.png) or move them under a test/ folder after verification.

9) Versioning & release notes
   - Tag `v1.4.1` (already in project.json/README). After the multi-category sweep and additional tuning, ship `v1.4.2` with final prompt tuning and workflow changes.

Rollback plan (if prompt changes degrade quality)
- Revert to the commit before enhanced_prompt_generator integration (git supports this). Steps:
  1. `git checkout -- auto_stock_creator.py` (or `git revert <commit>`)
  2. Re-run a short smoke test (single-keyword) to confirm behavior.
  3. Restore prompt logging to diagnose differences.

Acceptance criteria
- Multi-category A/B tests show equal or improved QA pass rates for negatives variant.
- Production hourly run produces new DB rows and homepage “Recently Added” updates with high-quality images.
- No increase in generation failures logged in `automation_failures.log`.

Notes & rationale
- Adding explicit negative constraints is low-risk and often yields large practical gains in avoiding hallucinated anatomy and gibberish text.
- Sampler/step tuning can yield larger quality gains but increases compute/time per image; test carefully.

Files to inspect tomorrow
- `auto_stock_creator.py` (production generator)
- `enhanced_prompt_generator.py` (prompt templates and per-category overrides)
- `scripts/ab_test_prompts.py` (A/B test harness)
- `scripts/index_images.php` (indexer timestamp update)
- `automation_failures.log`
- `ab_test_results.json`
- `prompt_logs/` (if created)

Estimated time
- A/B multi-category sweep (N=8, 5 categories): ~30–60 minutes (network + generation time)
- Analysis and prompt tweaks: 1–2 hours
- Workflow tuning and final test: 1–2 hours

End of plan
