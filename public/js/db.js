// public/js/db.js

window.MES_DB = {
    db: null,
    DB_NAME: "mes_db",
    DB_VERSION: 7, // increment when schema changes

    init() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.DB_NAME, this.DB_VERSION);

            request.onupgradeneeded = (e) => {
                const db = e.target.result;

                if (!db.objectStoreNames.contains("receivings")) {
                    db.createObjectStore("receivings", { keyPath: "id" });
                }

                if (!db.objectStoreNames.contains("sync_queue")) {
                    db.createObjectStore("sync_queue", { keyPath: "id" });
                }

                if (!db.objectStoreNames.contains("acid_testings")) {
                    db.createObjectStore("acid_testings", {
                        keyPath: "id",
                        autoIncrement: true
                    });
                }
            };

            request.onsuccess = (e) => {
                this.db = e.target.result;
                console.log("IndexedDB initialized");
                resolve(this.db);
            };

            request.onerror = () => reject("IndexedDB init failed");
        });
    }
};