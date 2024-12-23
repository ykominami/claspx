require "git"

module Claspx
  class GitCmd < Cmd
    def initialize(working_dir, logger = nil)
      @logger = logger
      @logger ||= Loggerx.loggerx
      @g = nil
      @working_dir_pn = nil
      @repo_dir_pn = nil
      open_working_dir(working_dir)
      super("git")
    end

    def open_working_dir(working_dir)
      @logger = Loggerx.loggerx if @logger.nil?
      @working_dir_pn = Pathname.new(working_dir)
      # GIT_EXT という名前のディレクトリをリポジトリとみなす
      @repo_dir_pn = @working_dir_pn.join(GIT_EXT)
      @g = if @repo_dir_pn.exist?
             # リポジトリがワーキングディレクトリの直下に存在すると指定する
             Git.open(@working_dir_pn.to_s, { repository: @repo_dir_pn, log: @logger })
           else
             # ワーキングディレクトリの直下にリポジトリを作成する（デフォルト）
             Git.init(@repo_dir_pn.to_s)
           end
    rescue ArgumentError => e
      @logger.fatal("An error occurred: #{e.message}")
    end

    def valid_repo?
      !@g.nil? && !@repo_dir.nil?
    end

    def add(path)
      @g&.add(path)
    end

    def commit(message)
      @g&.commit(message)
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
