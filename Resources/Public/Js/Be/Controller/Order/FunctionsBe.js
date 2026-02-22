ws.forms.controller.order.functions = {
    dbName: 'wsf_stripe_db',
    storeName: 'stripe_intents',

    initDb: function() {
        return new Promise((resolve, reject) => {
            let request = indexedDB.open(this.dbName, 1);
            request.onupgradeneeded = (event) => {
                let db = event.target.result;
                if (!db.objectStoreNames.contains(this.storeName)) {
                    db.createObjectStore(this.storeName);
                }
            };
            request.onsuccess = (event) => resolve(event.target.result);
            request.onerror = (event) => reject(event.target.error);
        });
    },
    addToStripeIndexedDb: async function(stripepiid, orderData) {
        orderData.wsf_timestamp = new Date().getTime(); // Zeitstempel hinzufügen
        let db = await this.initDb();
        let transaction = db.transaction([this.storeName], 'readwrite');
        let store = transaction.objectStore(this.storeName);
        store.put(orderData, stripepiid);
    },
    putToStripeIndexedDb: async function(stripepiid, orderData) {
        // Gleiche Logik wie add für put (update)
        await this.addToStripeIndexedDb(stripepiid, orderData);
    },
    getFromStripeIndexedDb: async function(stripepiid) {
        let db = await this.initDb();
        return new Promise((resolve, reject) => {
            let transaction = db.transaction([this.storeName], 'readonly');
            let store = transaction.objectStore(this.storeName);
            let request = store.get(stripepiid);
            request.onsuccess = (event) => {
                let result = event.target.result;
                if (result && result.wsf_timestamp) {
                    let now = new Date().getTime();
                    let twentyFourHours = 24 * 60 * 60 * 1000;
                    if (now - result.wsf_timestamp > twentyFourHours) {
                        // Daten zu alt, löschen und null zurückgeben
                        this.deleteFromStripeIndexedDb(stripepiid);
                        resolve(null);
                        return;
                    }
                }
                resolve(result);
            };
            request.onerror = (event) => reject(event.target.error);
        });
    },
    deleteFromStripeIndexedDb: async function(stripepiid) {
        let db = await this.initDb();
        let transaction = db.transaction([this.storeName], 'readwrite');
        let store = transaction.objectStore(this.storeName);
        store.delete(stripepiid);
    }
};
