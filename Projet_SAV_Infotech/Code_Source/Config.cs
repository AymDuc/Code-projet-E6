namespace InfotechSAVManager;

public static class Config
{
    public const string AppName = "Infotech SAV Manager";

    // Facile à modifier pendant l'épreuve BTS
    public static readonly string[] Statuts =
    {
        "Reçu",
        "Diagnostic",
        "En attente client",
        "Pièce commandée",
        "Réparation",
        "Terminé",
        "Restitué"
    };

    public static readonly string[] TypesAppareils =
    {
        "PC portable",
        "Unité centrale",
        "Imprimante",
        "Écran",
        "Onduleur",
        "Autre"
    };
}
