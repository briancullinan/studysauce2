select name as username,
  name as username_canonical,
  mail as email,
  mail as email_canonical,
  IF((SELECT count(*)
     FROM studysauce3.users_roles,studysauce3.role
     WHERE users_roles.uid = users.uid
        and role.rid = users_roles.rid
        and role.name = 'adviser') > 0, 'a:1:{i:0;s:12:"ROLE_ADVISER";}',
     IF((SELECT count(*)
        FROM studysauce3.users_roles,studysauce3.role
        WHERE users_roles.uid = users.uid
              and role.rid = users_roles.rid
              and role.name = 'master adviser') > 0, 'a:1:{i:0;s:12:"ROLE_ADVISER";}',
        IF((SELECT count(*)
            FROM studysauce3.users_roles,studysauce3.role
            WHERE users_roles.uid = users.uid
                  and role.rid = users_roles.rid
                  and role.name = 'parent') > 0, 'a:1:{i:0;s:11:"ROLE_PARENT";}',
           IF((SELECT count(*)
               FROM studysauce3.users_roles,studysauce3.role
               WHERE users_roles.uid = users.uid
                     and role.rid = users_roles.rid
                     and role.name = 'partner') > 0, 'a:1:{i:0;s:12:"ROLE_PARTNER";}',
              IF((SELECT count(*)
                  FROM studysauce3.users_roles,studysauce3.role
                  WHERE users_roles.uid = users.uid
                        and role.rid = users_roles.rid
                        and role.name = 'administrator') > 0, 'a:1:{i:0;s:10:"ROLE_ADMIN";}', 'a:0:{}'))))) as roles
from studysauce3.users;