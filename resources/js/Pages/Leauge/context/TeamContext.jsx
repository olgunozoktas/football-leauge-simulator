import { createContext, useContext, useState, useEffect } from "react";
import axios from "axios";

const TeamContext = createContext(undefined);

export const TeamProvider = ({ children }) => {
  const [teams, setTeams] = useState([]);
  const [fixtures, setFixtures] = useState([]);
  const [currentWeek, setCurrentWeek] = useState(0);
  const [isSimulationComplete, setIsSimulationComplete] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchInitialData();
  }, []);

  const fetchInitialData = async () => {
    try {
      setLoading(true);
      setError(null);

      const stateResponse = await axios.get('/api/simulation/state');
      setCurrentWeek(stateResponse.data.current_week);
      setIsSimulationComplete(stateResponse.data.is_simulation_complete);

      const teamsResponse = await axios.get('/api/teams');
      setTeams(teamsResponse.data);

      const fixturesResponse = await axios.get('/api/fixtures');
      setFixtures(fixturesResponse.data);

      setLoading(false);
    } catch (err) {
      setError('Failed to load initial data');
      setLoading(false);
      console.error('Error fetching initial data:', err);
    }
  };

  const simulateMatch = async (matchId) => {
    try {
      setLoading(true);
      setError(null);

      const response = await axios.post(`/api/simulation/match/${matchId}`);

      setFixtures(prevFixtures => {
        return prevFixtures.map(fixture => {
          if (fixture.id === matchId) {
            return response.data.match;
          }
          return fixture;
        });
      });

      const teamsResponse = await axios.get('/api/teams');
      setTeams(teamsResponse.data);

      setLoading(false);
    } catch (err) {
      setError('Failed to simulate match');
      setLoading(false);
      console.error('Error simulating match:', err);
    }
  };

  const simulateNextWeek = async () => {
    try {
      setLoading(true);
      setError(null);

      const response = await axios.post('/api/simulation/next-week');

      setCurrentWeek(response.data.week);
      setIsSimulationComplete(response.data.is_complete);
      setTeams(response.data.teams);

      const fixturesResponse = await axios.get('/api/fixtures');
      setFixtures(fixturesResponse.data);

      setLoading(false);
    } catch (err) {
      setError('Failed to simulate next week');
      setLoading(false);
      console.error('Error simulating next week:', err);
    }
  };

  const simulateAllWeeks = async () => {
    try {
      setLoading(true);
      setError(null);

      const response = await axios.post('/api/simulation/all-weeks');

      setIsSimulationComplete(response.data.is_complete);
      setTeams(response.data.teams);
      setCurrentWeek(6); // Set to final week

      const fixturesResponse = await axios.get('/api/fixtures');
      setFixtures(fixturesResponse.data);

      setLoading(false);
    } catch (err) {
      setError('Failed to simulate all weeks');
      setLoading(false);
      console.error('Error simulating all weeks:', err);
    }
  };

  const resetSimulation = async () => {
    try {
      setLoading(true);
      setError(null);

      const response = await axios.post('/api/simulation/reset');

      setCurrentWeek(response.data.current_week);
      setIsSimulationComplete(response.data.is_simulation_complete);
      setTeams(response.data.teams);
      setFixtures(response.data.fixtures);

      setLoading(false);
    } catch (err) {
      setError('Failed to reset simulation');
      setLoading(false);
      console.error('Error resetting simulation:', err);
    }
  };

  const updateMatchResult = async (matchId, homeGoals, awayGoals) => {
    try {
      setLoading(true);
      setError(null);

      const response = await axios.put(`/api/fixtures/${matchId}/result`, {
        home_goals: homeGoals,
        away_goals: awayGoals
      });

      setFixtures(prevFixtures => {
        return prevFixtures.map(fixture => {
          if (fixture.id === matchId) {
            return response.data.match;
          }
          return fixture;
        });
      });

      const teamsResponse = await axios.get('/api/teams');
      setTeams(teamsResponse.data);

      if (currentWeek >= 4) {
        updatePredictions();
      }

      setLoading(false);
    } catch (err) {
      setError('Failed to update match result');
      setLoading(false);
      console.error('Error updating match result:', err);
    }
  };

  const updatePredictions = async () => {
    try {
      setLoading(true);
      setError(null);

      const response = await axios.post('/api/simulation/predictions');

      setTeams(response.data.teams);

      setLoading(false);
    } catch (err) {
      setError('Failed to update predictions');
      setLoading(false);
      console.error('Error updating predictions:', err);
    }
  };

  return (
    <TeamContext.Provider
      value={{
        teams,
        fixtures,
        currentWeek,
        isSimulationComplete,
        loading,
        error,
        simulateMatch,
        simulateNextWeek,
        simulateAllWeeks,
        resetSimulation,
        updateMatchResult,
        fetchInitialData,
        updatePredictions,
      }}
    >
      {children}
    </TeamContext.Provider>
  );
};

// Custom hook to use the context
export const useTeamContext = () => {
  const context = useContext(TeamContext);
  if (context === undefined) {
    throw new Error("useTeamContext must be used within a TeamProvider");
  }
  return context;
};

