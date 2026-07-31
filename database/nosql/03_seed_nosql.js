// Vite & Gourmand
// Projection NoSQL de demonstration pour MongoDB / mongosh
// Etape 05.10 - Statistiques par menu
//
// Source : commandes du fichier MySQL 02_seed.sql.
// Ce fichier ne contient aucune donnee personnelle.
//
// Execution :
//   mongosh "mongodb://localhost:27017" 03_seed_nosql.js
//
// ATTENTION : seed de developpement uniquement.
// Le script est reexecutable et deterministe : il recree entierement la
// collection avant d'inserer les neuf projections de commande.

const statsDb = db.getSiblingDB("vite_et_gourmand_statistiques");
const collectionName = "commandes_statistiques";

const validator = {
    $jsonSchema: {
        bsonType: "object",
        required: [
            "_id",
            "version_schema",
            "menu",
            "date_prestation",
            "statut",
            "prix_total",
            "synchronise_le"
            ],
            additionalProperties: false,
            properties: {
                _id: {
                    bsonType: ["int", "long"],
                    minimum: 1,
                    description: "Identifiant de la commande MySQL"
                },
            version_schema: {
                bsonType: "int",
                minimum: 1
            },
            menu: {
                bsonType: "object",
                required: ["id", "titre"],
                    additionalProperties: false,
                    properties: {
                        id: {
                            bsonType: ["int", "long"],
                            minimum: 1
                        },
                    titre: {
                        bsonType: "string",
                        minLength: 1
                    }
                }
            },
            date_prestation: {
                bsonType: "date"
            },
            statut: {
                enum: [
                    "en_attente",
                    "acceptee",
                    "en_preparation",
                    "en_cours_de_livraison",
                    "livree",
                    "en_attente_retour_materiel",
                    "terminee",
                    "annulee"
                ]
            },
            prix_total: {
                bsonType: "decimal",
                minimum: Decimal128("0.00")
            },
            synchronise_le: {
                bsonType: "date"
            }
        }
    }
};

if (statsDb.getCollectionNames().includes(collectionName)) {
    assert.eq(
        true,
        statsDb.getCollection(collectionName).drop(),
        "La collection de demonstration n'a pas pu etre reinitialisee."
    );
}

assert.commandWorked(
    statsDb.createCollection(collectionName, {
        validator,
        validationLevel: "strict",
        validationAction: "error"
    })
);

const commandesStatistiques = statsDb.getCollection(collectionName);

commandesStatistiques.createIndex(
    {
        statut: 1,
        date_prestation: 1
    },
    {
        name: "idx_statut_date_prestation"
    }
);

commandesStatistiques.createIndex(
    {
        statut: 1,
        "menu.id": 1,
        date_prestation: 1
    },
    {
        name: "idx_statut_menu_date_prestation"
    }
);

const synchroniseLe = new Date();

const documents = [
    {
        _id: 1,
        version_schema: 1,
        menu: {
            id: 1,
            titre: "Déjeuner bordelais"
        },
        date_prestation: ISODate("2026-08-05T00:00:00.000Z"),
        statut: "en_attente",
        prix_total: Decimal128("120.00"),
        synchronise_le: synchroniseLe
    },
    {
        _id: 2,
        version_schema: 1,
        menu: {
            id: 1,
            titre: "Déjeuner bordelais"
        },
        date_prestation: ISODate("2026-08-02T00:00:00.000Z"),
        statut: "acceptee",
        prix_total: Decimal128("190.02"),
        synchronise_le: synchroniseLe
    },
    {
        _id: 3,
        version_schema: 1,
        menu: {
            id: 1,
            titre: "Déjeuner bordelais"
        },
        date_prestation: ISODate("2026-07-30T00:00:00.000Z"),
        statut: "en_preparation",
        prix_total: Decimal128("270.00"),
        synchronise_le: synchroniseLe
    },
    {
        _id: 4,
        version_schema: 1,
        menu: {
            id: 2,
            titre: "Noël prestige"
        },
        date_prestation: ISODate("2026-07-31T00:00:00.000Z"),
        statut: "annulee",
        prix_total: Decimal128("250.43"),
        synchronise_le: synchroniseLe
    },
    {
        _id: 5,
        version_schema: 1,
        menu: {
            id: 2,
            titre: "Noël prestige"
        },
        date_prestation: ISODate("2026-06-15T00:00:00.000Z"),
        statut: "terminee",
        prix_total: Decimal128("432.00"),
        synchronise_le: synchroniseLe
    },
    {
        _id: 6,
        version_schema: 1,
        menu: {
            id: 1,
            titre: "Déjeuner bordelais"
        },
        date_prestation: ISODate("2026-05-20T00:00:00.000Z"),
        statut: "terminee",
        prix_total: Decimal128("159.31"),
        synchronise_le: synchroniseLe
    },
    {
        _id: 7,
        version_schema: 1,
        menu: {
            id: 2,
            titre: "Noël prestige"
        },
        date_prestation: ISODate("2026-07-20T00:00:00.000Z"),
        statut: "en_attente_retour_materiel",
        prix_total: Decimal128("320.00"),
        synchronise_le: synchroniseLe
    },
    {
        _id: 8,
        version_schema: 1,
        menu: {
            id: 1,
            titre: "Déjeuner bordelais"
        },
        date_prestation: ISODate("2026-06-20T00:00:00.000Z"),
        statut: "en_attente_retour_materiel",
        prix_total: Decimal128("251.19"),
        synchronise_le: synchroniseLe
    },
    {
        _id: 9,
        version_schema: 1,
        menu: {
            id: 2,
            titre: "Noël prestige"
        },
        date_prestation: ISODate("2026-06-05T00:00:00.000Z"),
        statut: "terminee",
        prix_total: Decimal128("396.00"),
        synchronise_le: synchroniseLe
    }
];

const insertion = commandesStatistiques.insertMany(documents, {
    ordered: true
});

assert.eq(true, insertion.acknowledged);
assert.eq(documents.length, insertion.insertedIds.length);

// ---------------------------------------------------------------------------
// Controles de reference
// ---------------------------------------------------------------------------

const commandesParMenu = commandesStatistiques.aggregate([
    {
        $match: {
            statut: {
                $ne: "annulee"
            }
        }
    },
    {
        $group: {
            _id: "$menu.id",
            titre_menu: {
                $first: "$menu.titre"
            },
            nombre_commandes: {
                $sum: 1
            }
        }
    },
    {
        $project: {
            _id: 0,
            id_menu: "$_id",
            titre_menu: 1,
            nombre_commandes: 1
        }
    },
    {
        $sort: {
            id_menu: 1
        }
    }
]).toArray();

function chiffreAffairesParMenu(
    dateDebut,
    dateFinExclusive,
    idMenu = null
) {
    const filtre = {
        statut: "terminee",
        date_prestation: {
            $gte: dateDebut,
            $lt: dateFinExclusive
        }
    };

    if (idMenu !== null) {
        filtre["menu.id"] = idMenu;
    }

    return commandesStatistiques.aggregate([
        {
            $match: filtre
        },
        {
            $group: {
                _id: "$menu.id",
                titre_menu: {
                    $first: "$menu.titre"
                },
                chiffre_affaires: {
                    $sum: "$prix_total"
                }
            }
        },
        {
            $project: {
                _id: 0,
                id_menu: "$_id",
                titre_menu: 1,
                chiffre_affaires: 1
            }
        },
        {
            $sort: {
                id_menu: 1
            }
        }
    ]).toArray();
}

function chiffreAffairesTotal(dateDebut, dateFinExclusive) {
    return commandesStatistiques.aggregate([
        {
            $match: {
                statut: "terminee",
                date_prestation: {
                    $gte: dateDebut,
                    $lt: dateFinExclusive
                }
            }
        },
        {
            $group: {
                _id: null,
                chiffre_affaires: {
                    $sum: "$prix_total"
                }
            }
        },
        {
            $project: {
                _id: 0,
                chiffre_affaires: 1
            }
        }
    ]).toArray();
}

const debut2026 = ISODate("2026-01-01T00:00:00.000Z");
const debut2027 = ISODate("2027-01-01T00:00:00.000Z");

const chiffreAffaires2026 = chiffreAffairesParMenu(
    debut2026,
    debut2027
);
const chiffreAffairesNoel2026 = chiffreAffairesParMenu(
    debut2026,
    debut2027,
    2
);
const chiffreAffairesMai2026 = chiffreAffairesParMenu(
    ISODate("2026-05-01T00:00:00.000Z"),
    ISODate("2026-06-01T00:00:00.000Z")
);
const chiffreAffairesJuin2026 = chiffreAffairesParMenu(
    ISODate("2026-06-01T00:00:00.000Z"),
    ISODate("2026-07-01T00:00:00.000Z")
);
const chiffreAffairesJuillet2026 = chiffreAffairesParMenu(
    ISODate("2026-07-01T00:00:00.000Z"),
    ISODate("2026-08-01T00:00:00.000Z")
);
const chiffreAffairesTotal2026 = chiffreAffairesTotal(
    debut2026,
    debut2027
);

assert.eq(9, commandesStatistiques.countDocuments({}));
assert.eq(8, commandesStatistiques.countDocuments({
    statut: {
        $ne: "annulee"
    }
}));
assert.eq(1, commandesStatistiques.countDocuments({
    statut: "annulee"
}));
assert.eq(3, commandesStatistiques.countDocuments({
    statut: "terminee"
}));

assert.eq(2, commandesParMenu.length);
assert.eq(1, commandesParMenu[0].id_menu);
assert.eq("Déjeuner bordelais", commandesParMenu[0].titre_menu);
assert.eq(5, commandesParMenu[0].nombre_commandes);
assert.eq(2, commandesParMenu[1].id_menu);
assert.eq("Noël prestige", commandesParMenu[1].titre_menu);
assert.eq(3, commandesParMenu[1].nombre_commandes);

assert.eq(2, chiffreAffaires2026.length);
assert.eq("159.31", chiffreAffaires2026[0].chiffre_affaires.toString());
assert.eq("828.00", chiffreAffaires2026[1].chiffre_affaires.toString());

assert.eq(1, chiffreAffairesNoel2026.length);
assert.eq(
    "828.00",
    chiffreAffairesNoel2026[0].chiffre_affaires.toString()
);

assert.eq(1, chiffreAffairesMai2026.length);
assert.eq(
    "159.31",
    chiffreAffairesMai2026[0].chiffre_affaires.toString()
);

assert.eq(1, chiffreAffairesJuin2026.length);
assert.eq(
    "828.00",
    chiffreAffairesJuin2026[0].chiffre_affaires.toString()
);

assert.eq(0, chiffreAffairesJuillet2026.length);

assert.eq(1, chiffreAffairesTotal2026.length);
assert.eq(
    "987.31",
    chiffreAffairesTotal2026[0].chiffre_affaires.toString()
);

printjson({
    collection: `${statsDb.getName()}.${collectionName}`,
    documents_du_seed: commandesStatistiques.countDocuments({}),
    commandes_non_annulees_par_menu: commandesParMenu,
    chiffre_affaires_2026_par_menu: chiffreAffaires2026,
    chiffre_affaires_total_2026:
        chiffreAffairesTotal2026[0].chiffre_affaires.toString(),
    juillet_2026_sans_commande_terminee:
        chiffreAffairesJuillet2026
});
