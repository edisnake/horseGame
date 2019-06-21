create table horse
(
    id integer not null
        constraint horse_pkey
            primary key,
    nick_name varchar(255) not null,
    speed double precision not null,
    strength double precision not null,
    endurance double precision not null,
    autonomy double precision not null,
    slowdown double precision not null,
    best_speed double precision not null
);

alter table horse owner to postgres;

create table race
(
    id integer not null
        constraint race_pkey
            primary key,
    active integer not null,
    created_at timestamp(0) not null,
    max_distance double precision not null,
    completed_at timestamp(0) default NULL::timestamp without time zone,
    duration double precision
);

alter table race owner to postgres;

create table horse_by_race
(
    id integer not null
        constraint horse_by_race_pkey
            primary key,
    horse_id integer not null
        constraint fk_51ec718c76b275ad
            references horse,
    race_id integer not null
        constraint fk_51ec718c6e59d40d
            references race,
    current_distance double precision not null,
    spent_time double precision not null
);

alter table horse_by_race owner to postgres;

create index idx_51ec718c76b275ad
    on horse_by_race (horse_id);

create index idx_51ec718c6e59d40d
    on horse_by_race (race_id);

