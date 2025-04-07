import random
import math
import matplotlib.pyplot as plt

# Configuration des paramètres
num_systems = 10  # Nombre de systèmes à générer
max_planets_per_system = 8  # Nombre maximum de planètes par système

# Distribution des niveaux de colonisation (en pourcentages)
colonization_distribution = {
    1: 50,
    2: 20,
    3: 15,
    4: 10,
    5: 5
}

# Fonction pour générer un niveau de colonisation basé sur la distribution
def get_colonization_level():
    levels = []
    for level, percentage in colonization_distribution.items():
        levels.extend([level] * percentage)
    return random.choice(levels)

# Fonction pour générer des coordonnées avec une distribution gaussienne
def generate_coordinates():
    std_dev_x_y = 1000  # Écart-type pour X et Y
    std_dev_z = 10  # Écart-type pour Z (100 fois plus petit que X et Y)
    
    x = random.gauss(0, std_dev_x_y)
    y = random.gauss(0, std_dev_x_y)
    z = random.gauss(0, std_dev_z)
    
    return int(x), int(y), int(z)

# Génération des systèmes et des planètes
univers = []
for system_id in range(1, num_systems + 1):
    system_name = f"Système_{system_id}"
    x, y, z = generate_coordinates()
    univers.append((system_id, system_name, x, y, z))
    
    num_planets = random.randint(0, max_planets_per_system)
    for planet_id in range(1, num_planets + 1):
        planet_name = f"Planète_{system_id}_{planet_id}"
        niveau_colonisation = get_colonization_level()
        px, py = x + random.randint(-10, 10), y + random.randint(-10, 10)
        univers.append((None, planet_name, system_id, niveau_colonisation, px, py))

# Génération du script SQL
with open('univers.sql', 'w') as f:
    f.write("CREATE TABLE IF NOT EXISTS système (\n")
    f.write("    id INT AUTO_INCREMENT PRIMARY KEY,\n")
    f.write("    nom VARCHAR(255),\n")
    f.write("    x INT,\n")
    f.write("    y INT,\n")
    f.write("    z INT\n")
    f.write(");\n\n")

    f.write("CREATE TABLE IF NOT EXISTS planete (\n")
    f.write("    id INT AUTO_INCREMENT PRIMARY KEY,\n")
    f.write("    nom VARCHAR(255),\n")
    f.write("    systeme_id INT,\n")
    f.write("    niveau_colonisation INT,\n")
    f.write("    x INT,\n")
    f.write("    y INT,\n")
    f.write("    FOREIGN KEY (systeme_id) REFERENCES système(id)\n")
    f.write(");\n\n")

    for item in univers:
        if item[0] is not None:
            f.write(f"INSERT INTO système (id, nom, x, y, z) VALUES ({item[0]}, '{item[1]}', {item[2]}, {item[3]}, {item[4]});\n")
        else:
            f.write(f"INSERT INTO planete (nom, systeme_id, niveau_colonisation, x, y) VALUES ('{item[1]}