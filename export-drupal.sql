
/* copy users
INSERT OR IGNORE INTO ss_user(username, username_canonical, email, email_canonical, roles, created, enabled, locked, expired, credentials_expired, password, salt, first, last) VALUES ('bjcullinan', 'bjcullinan', 'admin@studysauce.com', 'admin@studysauce.com', 'a:1:{i:0;s:10:"ROLE_ADMIN";}', '2014-08-23 15:47:50', 1, 0, 0, 0, 'Q0pER0g2HhhsoGTttQXI16cxgvb7gM9bVPERG9Afiug', '$S$DYbMXewEA', 'Brian', 'Cullinan');
*/

select concat('INSERT OR IGNORE INTO ss_user(username, username_canonical, email, email_canonical, roles, created, last_login, enabled, locked, expired, credentials_expired, password, salt, first, last)',
              ' VALUES (',quote(if(username is null,'',username)),',',quote(if(username_canonical is null,'',username_canonical)),',',
              quote(if(email is null,'',email)),',',quote(if(email_canonical is null,'',email_canonical)),
              ',',if(roles is null,'null',quote(roles)),',\'',created,'\',',if(last_login is null,'null',concat('\'',last_login,'\'')),',',
              enabled,',',locked,',',expired,',',credentials_expired,',',quote(if(password is null,'',password)),',',
              quote(if(salt is null,'',salt)),',',quote(if(first is null,'',first)),',',quote(if(last is null,'',last)),');') as ins
from (
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
         FROM_UNIXTIME(access) as last_login,
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
           on ln.entity_id = uid and ln.entity_type = 'user'
     ) as users

union
/* copy groups
INSERT OR IGNORE INTO ss_user_group(user_id, group_id) VALUES ((SELECT id from ss_user where email = 'marketing@studysauce.com'), (SELECT id from ss_group where name = 'Stephen''s list'));
*/

select concat('INSERT OR IGNORE INTO ss_group(name, description, created, roles) VALUES (',replace(quote(title),'\\\'','\'\''),',\'\',','\'', from_unixtime(created),'\',\'a:1:{i:0;s:9:"ROLE_PAID";}\');')
from (
       SELECT
         title,
         created
       FROM studysauce3.node
       WHERE type = 'adviser_membership'
     ) as groups

UNION

select concat('INSERT OR IGNORE INTO ss_user_group(user_id, group_id) VALUES ((SELECT id from ss_user where email = ',quote(if(mail is null,'',mail)),'),(SELECT id from ss_group where name = ',replace(quote(if(title is null,'',title)),'\\\'','\'\''),'));') as ins
from (
       SELECT
         mail,
         title
       FROM studysauce3.og_membership
         LEFT JOIN studysauce3.users
           ON uid = etid AND entity_type = 'user'
         LEFT JOIN studysauce3.node
           ON nid = gid AND group_type = 'node'
     ) as user_groups

UNION
/* copy schedules
INSERT OR IGNORE INTO ss_user_group(user_id, group_id) VALUES ((SELECT id from ss_user where email = 'marketing@studysauce.com'), (SELECT id from ss_group where name = 'Stephen''s list'));
*/

select concat('INSERT OR IGNORE INTO schedule(user_id, university, grades, weekends, sharp6am11am,',
              ' sharp11am4pm, sharp4pm9pm, sharp9pm2am, created) VALUES ((SELECT id from ss_user ',
              'where email = ',quote(if(title is null,'',title)),'),',quote(if(university is null,'',university)),',',quote(if(weekends is null,'',weekends)),',',quote(if(grades is null,'',grades)),
              ',',if(sharp6am11am is null,'null',sharp6am11am),',',if(sharp11am4pm is null,'null',sharp11am4pm),',',
              if(sharp4pm9pm is null,'null',sharp4pm9pm),',',if(sharp9pm2am is null,'null',sharp9pm2am),',\'',created,'\');') as ins
from (
       select title,
         field_university_value as university,
         replace(field_grades_value,'_','-') as grades,
         replace(field_weekends_value,'_','-') as weekends,
         field_6_am_11_am_value as sharp6am11am,
         field_11_am_4_pm_value as sharp11am4pm,
         field_4_pm_9_pm_value as sharp4pm9pm,
         field_9_pm_2_am_value as sharp9pm2am,
         from_unixtime(created) as created
       from studysauce3.node
         left join studysauce3.field_data_field_university
           on field_data_field_university.entity_id = node.nid
         left join studysauce3.field_data_field_grades
           on field_data_field_grades.entity_id = node.nid
         left join studysauce3.field_data_field_weekends
           on field_data_field_weekends.entity_id = node.nid
         left join studysauce3.field_data_field_6_am_11_am
           on field_data_field_6_am_11_am.entity_id = node.nid
         left join studysauce3.field_data_field_11_am_4_pm
           on field_data_field_11_am_4_pm.entity_id = node.nid
         left join studysauce3.field_data_field_4_pm_9_pm
           on field_data_field_4_pm_9_pm.entity_id = node.nid
         left join studysauce3.field_data_field_9_pm_2_am
           on field_data_field_9_pm_2_am.entity_id = node.nid
       where type = 'schedule'
     ) as schedules

UNION
/* copy courses */
select concat('INSERT OR IGNORE INTO course(schedule_id, name, type, study_type, study_difficulty, start_time, end_time,',
              ' created, deleted, dotw) VALUES ((SELECT schedule.id from ss_user,schedule where ss_user.id = schedule.user_id ',
              ' and email = ',quote(if(title is null,'',title)),'),',quote(name),',',quote(type),',',quote(if(study_type is null,'',study_type)),
              ',',quote(if(study_difficulty is null,'',study_difficulty)),',\'',start_time,'\',\'',end_time,'\',\'',now(),'\',0,\'',dotw,'\');') as ins
from (
       select title,
         field_class_name_value as name,
         if(field_event_type_value is null,'c',field_event_type_value) as type,
         field_study_type_value as study_type,
         field_study_difficulty_value as study_difficulty,
         field_time_value as start_time,
         field_time_value2 as end_time,
         replace(group_concat(field_day_of_the_week_value SEPARATOR ','),'weekly','Weekly') as dotw
       from studysauce3.field_data_field_classes
         left join studysauce3.node
           on nid = entity_id
         left join studysauce3.field_data_field_class_name
           on field_data_field_class_name.entity_id = field_data_field_classes.field_classes_value
         left join studysauce3.field_data_field_event_type
           on field_data_field_event_type.entity_id = field_data_field_classes.field_classes_value
         left join studysauce3.field_data_field_study_type
           on field_data_field_study_type.entity_id = field_data_field_classes.field_classes_value
         left join studysauce3.field_data_field_study_difficulty
           on field_data_field_study_difficulty.entity_id = field_data_field_classes.field_classes_value
         left join studysauce3.field_data_field_time
           on field_data_field_time.entity_id = field_data_field_classes.field_classes_value
         left join studysauce3.field_data_field_day_of_the_week
           on field_data_field_day_of_the_week.entity_id = field_data_field_classes.field_classes_value
       where (field_event_type_value = 'c' or field_event_type_value = 'o' or field_event_type_value is null)
             and trim(field_class_name_value) != '' and field_time_value is not null and field_time_value2 is not null
       GROUP BY field_data_field_day_of_the_week.entity_id
     ) as courses
where dotw is not null;

/* copy goals */

/* copy deadlines */



