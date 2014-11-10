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
                             and role.name = 'administrator') > 0, 'a:1:{i:0;s:10:"ROLE_ADMIN";}', 'a:0:{}'))))) as roles,
  FROM_UNIXTIME(created) as created,
  1 as enabled,
  0 as locked,
  0 as expired,
  0 as credentials_expired,
  SUBSTR(pass FROM 13) as password,
  SUBSTR(pass FROM 1 FOR 12) as salt,
  field_first_name_value as first,
  field_last_name_value as last
from studysauce3.users
  LEFT JOIN studysauce3.field_data_field_first_name fn
    on fn.entity_id = uid and fn.entity_type = 'user'
  LEFT JOIN studysauce3.field_data_field_last_name ln
    on ln.entity_id = uid and ln.entity_type = 'user';

