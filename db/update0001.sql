CREATE TABLE page_maintainers (
    id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
    page_id varchar(500) NOT NULL,
    maintainer varchar(50) NOT NULL
);

CREATE INDEX maintainer ON page_maintainers (maintainer);
