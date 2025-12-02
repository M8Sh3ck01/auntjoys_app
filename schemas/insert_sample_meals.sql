-- Sample Meals for Aunt Joy's Restaurant
-- Run this AFTER inserting categories
-- Note: image_path is set to NULL - you can add images via Admin panel

-- Main Dishes (category_id = 1)
INSERT INTO Meals (name, description, price, image_path, category_id, is_available) VALUES
('Nsima with Chicken', 'Traditional nsima served with grilled chicken and vegetables', 3500.00, NULL, 1, 1),
('Rice and Beef Stew', 'Steamed white rice with tender beef in rich tomato stew', 4000.00, NULL, 1, 1),
('Fried Fish with Chips', 'Crispy fried chambo fish served with golden chips', 4500.00, NULL, 1, 1),
('Chicken Curry with Rice', 'Aromatic chicken curry served with basmati rice', 4200.00, NULL, 1, 1),
('Nsima with Beans', 'Nsima served with seasoned beans and tomato relish', 2500.00, NULL, 1, 1);

-- Drinks (category_id = 2)
INSERT INTO Meals (name, description, price, image_path, category_id, is_available) VALUES
('Fresh Orange Juice', 'Freshly squeezed orange juice', 800.00, NULL, 2, 1),
('Coca Cola 500ml', 'Chilled Coca Cola bottle', 500.00, NULL, 2, 1),
('Fanta Orange 500ml', 'Chilled Fanta Orange bottle', 500.00, NULL, 2, 1),
('Mineral Water 500ml', 'Pure bottled water', 300.00, NULL, 2, 1),
('Maheu (Traditional Drink)', 'Sweet traditional fermented drink', 600.00, NULL, 2, 1),
('Fresh Passion Juice', 'Freshly made passion fruit juice', 900.00, NULL, 2, 1);

-- Desserts (category_id = 3)
INSERT INTO Meals (name, description, price, image_path, category_id, is_available) VALUES
('Vanilla Ice Cream', 'Creamy vanilla ice cream scoop', 1200.00, NULL, 3, 1),
('Chocolate Cake Slice', 'Rich chocolate cake with frosting', 1500.00, NULL, 3, 1),
('Fruit Salad', 'Fresh mixed fruits with cream', 1000.00, NULL, 3, 1),
('Banana Fritters', 'Sweet fried banana fritters', 800.00, NULL, 3, 1);

-- Appetizers (category_id = 4)
INSERT INTO Meals (name, description, price, image_path, category_id, is_available) VALUES
('Samosas (3 pieces)', 'Crispy vegetable or meat samosas', 900.00, NULL, 4, 1),
('Spring Rolls (4 pieces)', 'Golden fried spring rolls', 1200.00, NULL, 4, 1),
('Chips (Small)', 'Crispy golden french fries', 1000.00, NULL, 4, 1),
('Chicken Wings (5 pieces)', 'Spicy grilled chicken wings', 2500.00, NULL, 4, 1);

-- Breakfast (category_id = 5)
INSERT INTO Meals (name, description, price, image_path, category_id, is_available) VALUES
('Eggs and Toast', 'Two fried eggs with buttered toast', 1500.00, NULL, 5, 1),
('Porridge (Phala)', 'Traditional warm porridge with milk and sugar', 800.00, NULL, 5, 1),
('Omelette with Bread', 'Vegetable omelette served with fresh bread', 1800.00, NULL, 5, 1),
('Breakfast Special', 'Eggs, sausages, beans, and toast', 3000.00, NULL, 5, 1);

-- Fast Food (category_id = 6)
INSERT INTO Meals (name, description, price, image_path, category_id, is_available) VALUES
('Beef Burger', 'Juicy beef patty with lettuce, tomato, and sauce', 3500.00, NULL, 6, 1),
('Chicken Burger', 'Grilled chicken breast with fresh vegetables', 3800.00, NULL, 6, 1),
('Hot Dog', 'Grilled sausage in a bun with toppings', 2000.00, NULL, 6, 1),
('Chicken Wrap', 'Grilled chicken wrapped with vegetables', 3200.00, NULL, 6, 1);

-- Traditional (category_id = 7)
INSERT INTO Meals (name, description, price, image_path, category_id, is_available) VALUES
('Chambo with Nsima', 'Grilled chambo fish with traditional nsima', 5000.00, NULL, 7, 1),
('Ndiwo with Nsima', 'Traditional vegetable relish with nsima', 2000.00, NULL, 7, 1),
('Kapenta with Nsima', 'Fried kapenta fish with nsima', 2800.00, NULL, 7, 1),
('Beef Offal (Matumbo)', 'Traditional beef offal stew with nsima', 3500.00, NULL, 7, 1);

-- Vegetarian (category_id = 8)
INSERT INTO Meals (name, description, price, image_path, category_id, is_available) VALUES
('Vegetable Stir Fry', 'Mixed vegetables stir-fried with soy sauce', 2500.00, NULL, 8, 1),
('Bean Curry', 'Spiced beans in curry sauce with rice', 2800.00, NULL, 8, 1),
('Veggie Burger', 'Plant-based patty with fresh vegetables', 3000.00, NULL, 8, 1),
('Mixed Vegetable Salad', 'Fresh garden salad with dressing', 1800.00, NULL, 8, 1);

-- Note: Prices are in Malawi Kwacha (MWK)
-- You can add images and modify details via the Admin panel
