const oracledb = require('oracledb');

class Database {
    constructor(config) {
      this.config = config;
    }
  
    async getConnection() {
        try {
          const connection = await oracledb.getConnection(this.config);
          console.log('Database connection established successfully');
          return connection;
        } catch (error) {
          console.error('Error establishing database connection:', error);
          throw error;
        }
    }
      
  
    async closeConnection(connection) {
      try {
        await connection.close();
      } catch (error) {
        console.error('Error closing database connection:', error);
        throw error;
      }
    }
  
    
    async createMultiRow(tableName, column, data, given_connection = null) {
      let connection = given_connection;
      let isGivenConnection = false;
  
      try {
        if (!connection) {
          connection = await this.getConnection();
          await connection.execute("BEGIN");
        } else {
          isGivenConnection = true;
        }
  
        const noOfColumn = column.split(",").length;
        const valueBindings = Array.from({ length: noOfColumn }, (_, i) => `:${i + 1}`).join(",");
        const sql = `INSERT INTO ${tableName} (${column}) VALUES (${valueBindings})`;
  
        // const bindDefs = Array.from({ length: noOfColumn }, () => ({ type: oracledb.STRING, maxSize: 2000 }));
        const bindDefs = Array.from({ length: noOfColumn }, (_, i) => {
          let bindType = oracledb.STRING;
          let bindMaxSize = 2000;
        
          // Determine the appropriate bind type and maxSize based on the value type
          if (typeof data[0][i] === 'number') {
            bindType = oracledb.NUMBER;
            bindMaxSize = undefined;
          } else if (data[0][i] instanceof Date) {
            if (data[0][i].getHours() === 0 && data[0][i].getMinutes() === 0 && data[0][i].getSeconds() === 0) {
              bindType = oracledb.DB_TYPE_DATE;
            } else {
              bindType = oracledb.DB_TYPE_TIMESTAMP;
            }
            bindMaxSize = undefined;
          }
        
          return {
            type: bindType,
            maxSize: bindMaxSize,
          };
        });
        
  
        const options = {
          autoCommit: !isGivenConnection, // Set autoCommit to false when a connection is provided
          bindDefs: bindDefs,
        };
  
        const result = await connection.executeMany(sql, data, options);
  
        console.log("Number of rows inserted:", result.rowsAffected);
  
        return true;
      } catch (error) {
        console.error('Error executing CREATE operation:', error);
  
        if (!isGivenConnection && connection) {
          await connection.execute("ROLLBACK");
        }
  
        throw error;
      } finally {
        if (!isGivenConnection && connection) {
          if (!isGivenConnection) {
            await connection.execute("COMMIT");
          }
  
          await this.closeConnection(connection);
        }
      }
    }
  
    async create(tableName, data, given_connection = null) {
      let connection = given_connection;
      let isGivenConnection = false;
  
      try {
        if (!connection) {
          connection = await this.getConnection();
        } else {
          isGivenConnection = true;
        }
  
        const keys = Object.keys(data);
        const values = Object.values(data);
  
        const placeholders = keys.map((_, index) => `:${index + 1}`);
        const query = `INSERT INTO ${tableName} (${keys.join(', ')}) VALUES (${placeholders.join(', ')})`;
  
        const options = {
          autoCommit: !isGivenConnection,
          bindDefs: values.map((value, index) => ({
            type: typeof value === 'number' ? oracledb.NUMBER : oracledb.STRING,
            maxSize: typeof value === 'string' ? 2000 : undefined,
          })),
        };
  
        const result = await connection.execute(query, values, options);
  
        if (!isGivenConnection) {
          await connection.execute("COMMIT");
        }
  
        return result.rowsAffected === 1;
      } catch (error) {
        console.error('Error executing CREATE operation:', error);
  
        if (!isGivenConnection && connection) {
          await connection.execute("ROLLBACK");
        }
  
        throw error;
      } finally {
        if (!isGivenConnection && connection) {
          await this.closeConnection(connection);
        }
      }
    }
    
      
    async update(tableName, data, condition, given_connection = null) {
      let connection = given_connection;
      let isGivenConnection = false;
    
      try {
        if (!connection) {
          connection = await this.getConnection();
        } else {
          isGivenConnection = true;
        }
    
        const keys = Object.keys(data);
        const values = Object.values(data);
    
        const setClause = keys.map((key, index) => `${key} = :${index + 1}`).join(', ');
        const query = `UPDATE ${tableName} SET ${setClause} WHERE ${condition}`;
    
        const options = {
          autoCommit: !isGivenConnection,
            bindDefs: values.map((value, index) => {
            let bindType;
            let bindMaxSize;
          
            if (typeof value === 'number') {
              bindType = oracledb.NUMBER;
            } else if (typeof value === 'string') {
              bindType = oracledb.STRING;
              bindMaxSize = 2000;
            } else if (value instanceof Date) {
              if (value instanceof Date && value.getHours() === 0 && value.getMinutes() === 0 && value.getSeconds() === 0) {
                bindType = oracledb.DB_TYPE_DATE;
              } else {
                bindType = oracledb.DB_TYPE_TIMESTAMP;
              }
            }
          
            return {
              type: bindType,
              maxSize: bindMaxSize,
            };
          }),
        };
    
        const result = await connection.execute(query, values, options);
    
        if (!isGivenConnection) {
          await connection.execute("COMMIT");
        }
    
        return result.rowsAffected === 1;
      } catch (error) {
        console.error('Error executing UPDATE operation:', error);
    
        if (!isGivenConnection && connection) {
          await connection.execute("ROLLBACK");
        }
    
        throw error;
      } finally {
        if (!isGivenConnection && connection) {
          await this.closeConnection(connection);
        }
      }
    }
    
    async delete(tableName, condition, given_connection = null) {
      let connection = given_connection;
      let isGivenConnection = false;
    
      try {
        if (!connection) {
          connection = await this.getConnection();
        } else {
          isGivenConnection = true;
        }
    
        const query = `DELETE FROM ${tableName} WHERE ${condition}`;
    
        const options = {
          autoCommit: !isGivenConnection,
        };
    
        const result = await connection.execute(query, [], options);
    
        if (!isGivenConnection) {
          await connection.execute("COMMIT");
        }
    
        return result.rowsAffected === 1;
      } catch (error) {
        console.error('Error executing DELETE operation:', error);
    
        if (!isGivenConnection && connection) {
          await connection.execute("ROLLBACK");
        }
    
        throw error;
      } finally {
        if (!isGivenConnection && connection) {
          await this.closeConnection(connection);
        }
      }
    }
    
  
    async find(tableName, condition, given_connection = null) {
      let connection = given_connection;
      let isGivenConnection = false;
    
      try {
        if (!connection) {
          connection = await this.getConnection();
        } else {
          isGivenConnection = true;
        }
    
        const query = `SELECT * FROM ${tableName} WHERE ${condition}`;
        const result = await connection.execute(query);
        return result.rows;
      } catch (error) {
        console.error('Error executing FIND operation:', error);
        throw error;
      } finally {
        if (!isGivenConnection && connection) {
          await this.closeConnection(connection);
        }
      }
    }
    
    async findAll(tableName, given_connection = null) {
      let connection = given_connection;
      let isGivenConnection = false;
    
      try {
        if (!connection) {
          connection = await this.getConnection();
        } else {
          isGivenConnection = true;
        }
    
        const query = `SELECT * FROM ${tableName}`;
        const result = await connection.execute(query);
        return result.rows;
      } catch (error) {
        console.error('Error executing FIND ALL operation:', error);
        throw error;
      } finally {
        if (!isGivenConnection && connection) {
          await this.closeConnection(connection);
        }
      }
    }
    
    async getNextId(tableName,column = 'id', given_connection = null) {
      let connection = given_connection;
      let isGivenConnection = false;
    
      try {
        if (!connection) {
          connection = await this.getConnection();
        } else {
          isGivenConnection = true;
        }
    
        const query = `SELECT COALESCE(MAX(${column}) + 1, 1) AS nextId FROM ${tableName}`;
        const result = await connection.execute(query);
        console.log('Result rows:', result.rows); // Log the rows returned from the query
        console.log(`nextId=${result.rows[0][0]}, result.length=${result.rows.length}`);
        return result.rows[0][0];
      } catch (error) {
        console.error(`Error retrieving next ID:${column}`, error);
        throw error;
      } finally {
        if (!isGivenConnection && connection) {
          await this.closeConnection(connection);
        }
      }
    }
    
    
    async messageCount(tableName, given_connection = null) {
      let connection = given_connection;
      let isGivenConnection = false;
    
      try {
        if (!connection) {
          connection = await this.getConnection();
        } else {
          isGivenConnection = true;
        }
    
        const query = `SELECT COALESCE(count(id), 0) AS total FROM ${tableName}`;
        const result = await connection.execute(query);
        console.log('Result rows:', result.rows); // Log the rows returned from the query
        console.log(`messageCount=${result.rows[0][0]}, result.length=${result.rows.length}`);
        return result.rows[0][0];
      } catch (error) {
        console.error('Error retrieving messageCount:', error);
        throw error;
      } finally {
        if (!isGivenConnection && connection) {
          await this.closeConnection(connection);
        }
      }
    }
    
    async findValueById(query, given_connection = null) {
      let connection = given_connection;
      let isGivenConnection = false;
    
      try {
        if (!connection) {
          connection = await this.getConnection();
        } else {
          isGivenConnection = true;
        }
    
        const result = await connection.execute(query);
        console.log('Result rows:', result.rows); // Log the rows returned from the query
        console.log(`nextId=${result.rows[0][0]}, result.length=${result.rows.length}`);
        return result.rows[0][0];
      } catch (error) {
        console.error('Error retrieving next ID:', error);
        throw error;
      } finally {
        if (!isGivenConnection && connection) {
          await this.closeConnection(connection);
        }
      }
    }
    
    async isExist(table, column, value, update_column, return_column_value,value_added=1, given_connection = null) {
      let connection = given_connection;
      let isGivenConnection = false;
    
      try {
        if (!connection) {
          connection = await this.getConnection();
        } else {
          isGivenConnection = true;
        }
    
        const query = `SELECT ${return_column_value}, SUM(${update_column}) AS total FROM ${table} WHERE ${column} = :value GROUP BY ${return_column_value}`;
        const result = await connection.execute(query, [value]);
        let id = -1;
    
        if (result.rows.length > 0) {
          id = result.rows[0][0];
          const total = Number(result.rows[0][1]) + Number(value_added);
          await this.update(table, { [update_column]: total }, `${return_column_value} = ${id}`, connection);
        }
    
        if (!isGivenConnection) {
          await connection.execute("COMMIT");
        }
    
        return id;
      } catch (error) {
        console.error('Error executing isExist operation:', error);
    
        if (!isGivenConnection && connection) {
          await connection.execute("ROLLBACK");
        }
    
        return -404;
      } finally {
        if (!isGivenConnection && connection) {
          await this.closeConnection(connection);
        }
      }
    }
    
  

  async findObjectById(query, given_connection = null) {
    let connection = given_connection;
    let isGivenConnection = false;
  
    try {
      if (!connection) {
        connection = await this.getConnection();
      } else {
        isGivenConnection = true;
      }
  
      const result = await connection.execute(query);
      console.log('Result rows:', result.rows); // Log the rows returned from the query
  
      if (result.rows.length === 0) {
        return null; // Return null if no rows are found
      }
  
      const columns = result.metaData.map((column) => column.name); // Get the column names
      const values = result.rows[0]; // Get the first row
  
      const object = {};
      for (let i = 0; i < columns.length; i++) {
        object[columns[i]] = values[i]; // Assign each column value to the corresponding property in the object
      }
  
      return object;
    } catch (error) {
      console.error('Error retrieving object:', error);
      throw error;
    } finally {
      if (!isGivenConnection && connection) {
        await this.closeConnection(connection);
      }
    }
  }
  
  async findAllObjects(query, given_connection = null) {
    let connection = given_connection;
    let isGivenConnection = false;
  
    try {
      if (!connection) {
        connection = await this.getConnection();
      } else {
        isGivenConnection = true;
      }
  
      const result = await connection.execute(query);
      console.log('Result rows:', result.rows); // Log the rows returned from the query
  
      if (result.rows.length === 0) {
        return []; // Return an empty array if no rows are found
      }
  
      const columns = result.metaData.map((column) => column.name); // Get the column names
  
      const objects = result.rows.map((row) => {
        const object = {};
        for (let i = 0; i < columns.length; i++) {
          object[columns[i]] = row[i]; // Assign each column value to the corresponding property in the object
        }
        return object;
      });
  
      return objects;
    } catch (error) {
      console.error('Error retrieving objects:', error);
      throw error;
    } finally {
      if (!isGivenConnection && connection) {
        await this.closeConnection(connection);
      }
    }
  }
  
  async retrievePendingNotificationsFromDatabase(userId,given_connection=null)
  {
    let connection = given_connection;
    let isGivenConnection = false;
  
    try {
      if (!connection) {
        connection = await this.getConnection();
      } else {
        isGivenConnection = true;
      }
  
      const result = await connection.execute(`SELECT APP_NOTI_DESC as MESSAGE FROM NOTIFICATION_BREAK_DOWN WHERE NOTIFICATION_USER=${userId} AND IS_VIEWED = 0`);

      console.log(result);
      console.log('Result rows:', result.rows); // Log the rows returned from the query
  
      return result;
    } catch (error) {
      console.error('Error retrieving objects:', error);
      throw error;
    } finally {
      if (!isGivenConnection && connection) {
        await this.closeConnection(connection);
      }
    }
  }

  async clearPendingNotificationsFromDatabase(userId,given_connection=null)
  {
    //find(tableName, condition, given_connection = null)
    let connection = given_connection;
    let isGivenConnection = false;
  
    try {
      if (!connection) {
        connection = await this.getConnection();
      } else {
        isGivenConnection = true;
      }
      await this.update("NOTIFICATION_BREAK_DOWN", { ['IS_VIEWED']: 1 }, `NOTIFICATION_USER = ${userId}`, connection);
        
    } catch (error) {
      console.error('Error retrieving objects:', error);
      throw error;
    } finally {
      if (!isGivenConnection && connection) {
        await this.closeConnection(connection);
      }
    }
  }
  
}
  
module.exports = Database;