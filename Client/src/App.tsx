import { BrowserRouter, Routes, Route, Navigate } from "react-router";
import Header from "./components/Header";
import Home from "./pages/Home";
import Login from "./pages/Login";
import Register from "./pages/Register";
import Profile from "./pages/Profile";
import NotFound from "./pages/NotFound";
import Map from "./pages/Map";
import Upload from "./pages/Upload";
import Details from "./pages/Details";
import Challenges from "./pages/Challenges";
import Explore from "./pages/Explore";
import { useAuth } from "./auth/AuthContext";
import LoadingScreen from "./pages/Loading";
import Leaderboard from "./pages/Leaderboard";

function App() {
  const { isAuthenticated, isLoading } = useAuth();

  if (isLoading) {
    return <LoadingScreen />;
  }

  return (
    <BrowserRouter>
      <Header />
      <Routes>
        <Route path="/" element={isAuthenticated ? <Map /> : <Home />} />

        <Route
          path="/login"
          element={isAuthenticated ? <Navigate to="/" /> : <Login />}
        />

        <Route
          path="/register"
          element={isAuthenticated ? <Navigate to="/" /> : <Register />}
        />

        <Route
          path="/profile"
          element={isAuthenticated ? <Profile /> : <Navigate to="/login" />}
        />

        <Route path="*" element={<NotFound />} />

        <Route path="/app" element={isAuthenticated ? <Map /> : <Home />} />

        <Route path="/upload" element={<Upload />} />

        <Route path="/details" element={<Details />} />

        <Route path="/challenges" element={<Challenges />} />

        <Route
          path="/explore"
          element={isAuthenticated ? <Explore /> : <Navigate to="/" />}
        />

        <Route
          path="/leaderboard"
          element={isAuthenticated ? <Leaderboard /> : <Navigate to="/" />}
        />
        
      </Routes>
    </BrowserRouter>
  );
}

export default App;
