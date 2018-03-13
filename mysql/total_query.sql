SELECT ps.user_id, score.val
FROM (
SELECT game1 val FROM pinmaster_score WHERE user_id = 3
UNION ALL
SELECT game2 val FROM pinmaster_score WHERE user_id = 3
UNION ALL
SELECT game3 val FROM pinmaster_score WHERE user_id = 3
) score, pinmaster_score ps
WHERE ps.user_id = 3