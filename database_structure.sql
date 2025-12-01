CREATE TABLE materials
(
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description TEXT,
    unit        VARCHAR(50)  NOT NULL,
    unit_price  DECIMAL(10, 2),
    stock       INTEGER   DEFAULT 0,
    active      BOOLEAN   DEFAULT true,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE couriers
(
    id             SERIAL PRIMARY KEY,
    name           VARCHAR(255) NOT NULL,
    email          VARCHAR(255),
    delivery_zone  VARCHAR(100),
    available      BOOLEAN       DEFAULT true,
    average_rating DECIMAL(3, 2) DEFAULT 0.00,
    delivery_count INTEGER       DEFAULT 0,
    created_at     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE requests
(
    id               SERIAL PRIMARY KEY,
    material_id      INTEGER     NOT NULL REFERENCES materials (id),
    quantity         INTEGER     NOT NULL,
    delivery_address TEXT        NOT NULL,
    required_at      TIMESTAMP   NOT NULL,
    status           VARCHAR(50) NOT NULL DEFAULT 'PENDING',
    courier_id       INTEGER REFERENCES couriers (id),
    assigned_at      TIMESTAMP,
    delivered_at     TIMESTAMP,
    created_at       TIMESTAMP            DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP            DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_positive_quantity CHECK (quantity > 0),
    CONSTRAINT chk_valid_status CHECK (
        status IN ('PENDING', 'ASSIGNED', 'DELIVERED', 'CANCELLED')
        )
);

CREATE TABLE deliveries
(
    id                 SERIAL PRIMARY KEY,
    request_id         INTEGER   NOT NULL REFERENCES requests (id),
    courier_id         INTEGER   NOT NULL REFERENCES couriers (id),
    delivered_at       TIMESTAMP NOT NULL,
    required_at        TIMESTAMP NOT NULL,
    on_time            BOOLEAN GENERATED ALWAYS AS (delivered_at <= required_at) STORED,
    receiver_signature VARCHAR(255),
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE evaluations
(
    id             SERIAL PRIMARY KEY,
    delivery_id    INTEGER NOT NULL REFERENCES deliveries (id),
    courier_id     INTEGER NOT NULL REFERENCES couriers (id),
    on_time_rating INTEGER NOT NULL, -- 1 to 5
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_on_time_rating CHECK (on_time_rating BETWEEN 1 AND 5)
);
