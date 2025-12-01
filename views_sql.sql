CREATE
OR REPLACE VIEW materials_read AS
SELECT m.id,
       m.name,
       m.description,
       m.unit,
       m.unit_price,
       m.stock                      AS current_stock,
       m.active,
       COUNT(r.id)                  AS total_requests,
       COALESCE(SUM(r.quantity), 0) AS total_quantity_requested,
       m.created_at,
       m.updated_at
FROM materials m
         LEFT JOIN requests r ON m.id = r.material_id
GROUP BY m.id, m.name, m.description, m.unit, m.unit_price,
         m.stock, m.active, m.created_at, m.updated_at;



CREATE
OR REPLACE VIEW requests_read AS
SELECT r.id,
       r.material_id,
       m.name  AS material_name,
       m.unit  AS material_unit,
       r.quantity,
       r.delivery_address,
       r.required_at,
       r.status,
       r.courier_id,
       c.name  AS courier_name,
       r.assigned_at,
       r.delivered_at,
       r.created_at,
       r.updated_at,
       CASE
           WHEN r.delivered_at IS NOT NULL
               AND r.delivered_at <= r.required_at THEN true
           WHEN r.delivered_at IS NOT NULL
               AND r.delivered_at > r.required_at THEN false
           ELSE NULL
           END AS was_on_time
FROM requests r
         INNER JOIN materials m ON r.material_id = m.id
         LEFT JOIN couriers c ON r.courier_id = c.id;



CREATE
OR REPLACE VIEW deliveries_read AS
SELECT d.id,
       d.request_id,
       r.material_id,
       m.name  AS material_name,
       r.quantity,
       r.delivery_address,
       d.courier_id,
       c.name  AS courier_name,
       c.email AS courier_email,
       d.delivered_at,
       d.required_at,
       d.on_time,
       d.receiver_signature,
       ev.id   AS evaluation_id,
       ev.on_time_rating,
       d.created_at
FROM deliveries d
         INNER JOIN requests r ON d.request_id = r.id
         INNER JOIN materials m ON r.material_id = m.id
         INNER JOIN couriers c ON d.courier_id = c.id
         LEFT JOIN evaluations ev ON d.id = ev.delivery_id;



CREATE
OR REPLACE VIEW statistics_read AS
SELECT (SELECT COUNT(*) FROM requests)                                     AS total_requests,
       (SELECT COUNT(*) FROM requests WHERE status = 'PENDING')            AS pending_requests,
       (SELECT COUNT(*) FROM requests WHERE status = 'ASSIGNED')           AS assigned_requests,
       (SELECT COUNT(*) FROM requests WHERE status = 'DELIVERED')          AS delivered_requests,
       (SELECT COUNT(*) FROM requests WHERE status = 'CANCELLED')          AS cancelled_requests,


       (SELECT COUNT(*) FROM deliveries)                                   AS total_deliveries,
       (SELECT COUNT(*) FROM deliveries WHERE on_time = true)              AS on_time_deliveries,
       (SELECT COUNT(*) FROM deliveries WHERE on_time = false)             AS late_deliveries,
       (SELECT ROUND(
                       (COUNT(*) FILTER (WHERE on_time = true)::DECIMAL /
         NULLIF(COUNT(*), 0) * 100), 2
               )
        FROM deliveries)                                                   AS on_time_percentage,


       (SELECT COUNT(*) FROM evaluations)                                  AS total_evaluations,
       (SELECT ROUND(AVG(on_time_rating), 2) FROM evaluations)             AS avg_on_time_rating,


       (SELECT COUNT(*) FROM couriers)                                     AS total_couriers,
       (SELECT COUNT(*) FROM couriers WHERE available = true)              AS available_couriers,
       (SELECT COUNT(*) FROM couriers WHERE available = false)             AS unavailable_couriers,


       (SELECT COUNT(*) FROM materials WHERE active = true)                AS active_materials,
       (SELECT COUNT(*) FROM materials WHERE active = false)               AS inactive_materials,
       (SELECT COALESCE(SUM(stock), 0) FROM materials WHERE active = true) AS total_stock;



CREATE
OR REPLACE VIEW courier_ranking AS
SELECT c.id,
       c.name,
       c.delivery_zone,
       c.available,
       c.delivery_count,
       c.average_rating,
       COUNT(d.id)                      AS total_deliveries,
       COUNT(d.id)                         FILTER (WHERE d.on_time = true) AS on_time_deliveries, COUNT(d.id) FILTER (WHERE d.on_time = false) AS late_deliveries, ROUND(
        (COUNT(d.id) FILTER (WHERE d.on_time = true)::DECIMAL /
         NULLIF(COUNT(d.id), 0) * 100), 2
                                                                                                                                                                   ) AS on_time_percentage,
       ROUND(AVG(ev.on_time_rating), 2) AS on_time_rating
FROM couriers c
         LEFT JOIN deliveries d ON c.id = d.courier_id
         LEFT JOIN evaluations ev ON c.id = ev.courier_id
GROUP BY c.id, c.name, c.delivery_zone, c.available,
         c.delivery_count, c.average_rating



CREATE
OR REPLACE VIEW most_requested_materials AS
SELECT m.id,
       m.name,
       m.unit,
       COUNT(r.id)     AS total_requests,
       SUM(r.quantity) AS total_quantity,
       COUNT(r.id)        FILTER (WHERE r.status = 'DELIVERED') AS completed_requests, COUNT(r.id) FILTER (WHERE r.status = 'PENDING') AS pending_requests, COUNT(r.id) FILTER (WHERE r.status = 'ASSIGNED') AS in_progress_requests
FROM materials m
         LEFT JOIN requests r ON m.id = r.material_id
WHERE m.active = true
GROUP BY m.id, m.name, m.unit
ORDER BY total_requests DESC;


