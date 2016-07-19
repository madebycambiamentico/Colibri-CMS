
-- COMMENTS ***********************************************************

-- select ordered tree from closure table:
-- replace "p.ancestor = 1" with id of root comment (any in the tree)
SELECT d.*, p.*, GROUP_CONCAT(crumbs.ancestor,'.') AS breadcrumbs
FROM commenti AS d
JOIN commenti_treepath AS p ON d.id = p.descendant
JOIN commenti_treepath AS crumbs ON crumbs.descendant = p.descendant
WHERE p.ancestor = 1
GROUP BY d.id
ORDER BY breadcrumbs;


-- EMAILS ***********************************************************

-- get very first 3 email to send to user (name + email):
SELECT e.*, u.nome, u.email FROM
emails e
LEFT JOIN
utenti u
ORDER BY e.datacreaz ASC
LIMIT 3;

-- get all user id and n. pending email for all users
SELECT count(*), u.id FROM
emails e
LEFT JOIN
utenti u
GROUP BY u.id
ORDER BY u.id;

-- get n. pending email for user ID...
-- replace "iduser = 1" and "id = 1" with id of searched user
SELECT id, (SELECT count(*) FROM emails where iduser = 1) as 'pendingemails' FROM utenti WHERE id = 1