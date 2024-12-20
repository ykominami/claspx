require "git"

module Claspx
  class GitCmd < Cmd
    @logger = nil

    def self.open_git_repo(working_dir)
      g = nil
      begin
        if @logger.nil?
          @logger = Loggerx.loggerx      
        end
        g = Git.open(working_dir, log: @logger)
      rescue ArgumentError => e
        @logger.fatal( "An error occurred: #{e.message}" )
      end
      g
    end
    
    def initialize(repo_dir, logger = nil)
      @logger = logger
      @logger = Loggerx.loggerx unless @logger
      @g = nil
      @repo_dir = nil
      open_repo_dir(repo_dir)
    end

    def open_repo_dir(repo_dir)
      begin
        @g = Git.open(repo_dir, log: @logger)
      rescue ArgumentError => e
        @logger.fatal( "An error occurred: #{e.message}" )
      end
      if !@g.nil?
        @repo_dir = repo_dir        
      end
    end

    def valid_repo?
      !@g.nil? && !@repo_dir.nil?
    end

    def git_op(working_dir)
    end

    def git_op_level2(g)
      true
    end

    def git_op_level2_0(g)
      g.index
      g.index.readable?
      g.index.writable?
      g.repo
      g.dir

      g.ls_tree("HEAD", recursive: true)
      g.log
      g.log(200)
      g.log.since("2 weeks ago")
      g.log(200).since("2 weeks ago")
      g.log.between("v2.5", "v2.6")
      # g.log.each { |l| Loggerxcm.debug l.sha }
      g.gblob("v2.5:Makefile").log.since("2 weeks ago")
      Git.ls_remote

      true
    end
  end
end
